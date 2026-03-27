<?php

namespace Modules\TaxModule\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Modules\TaxModule\Entities\Tax;
use Modules\TaxModule\Exports\TaxVatExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\TaxModule\Traits\VatTaxConfiguration;

class TaxVatController extends Controller
{
    use VatTaxConfiguration;
    private Tax $taxVat;



    public function __construct(Tax $taxVat)
    {
        $this->taxVat = $taxVat;
    }

    /**
     * Displays the list of tax vat data.
     *
     * @return Renderable
     */
    public function index(Request $request): Renderable
    {
       $taxVats = $this->getData($request)->paginate($this->getpagination());
        return view($this->getProjectWiseViewPath('tax_list'), compact('taxVats'));
    }


    public function store(Request $request):RedirectResponse
    {
        $this->validateRequest($request);
        $this->createTaxVatData($request);
        $this->showNotification('successMessage', translate('messages.New_Tax_Added_Successfully'));
        return back();
    }

    public function update(Request $request, Tax $taxVat): RedirectResponse
    {
        $this->validateRequest($request, $taxVat->id);
        $this->updatetaxVat($request, $taxVat);
        $this->showNotification('successMessage',$taxVat->name . ' ' . translate('messages.updated_successfully'));
        return to_route('taxvat.index');
    }

    private function validateRequest(Request $request, $id = null): void
    {
        $request->validate(
            [
                'name' => 'required|max:50|unique:taxes,name' . ($id ? ',' . $id : ''),
                'country_code' => 'nullable|max:20|unique:taxes,country_code' . ($id ? ',' . $id : ''),
                'tax_rate' => 'required|numeric|max:100|min:0.001',

            ]
        );
    }

    private function createTaxVatData($request): Tax
    {
        $taxVat = $this->taxVat;
        return  $this->updatetaxVat($request, $taxVat);
    }

    private function updatetaxVat($request, $taxVat): Tax
    {
        $taxVat->name = $request->name;
        $taxVat->tax_rate = $request->tax_rate;
        $taxVat->is_default = true;
        if($this->getCountryType() != 'single'){
            $taxVat->country_code = $request->country_code ?? $taxVat?->country_code;
            $taxVat->is_default = false;
        }
        $taxVat->is_active = $request->status ?? 0;
        $taxVat->save();
        return $taxVat;
    }

    public function status(Tax $taxVat): JsonResponse
    {
        $taxVat->update(['is_active' => !$taxVat->is_active]);
        return response()->json(['id' => $taxVat->id ,'status' =>  $taxVat->is_active , 'message' => translate('messages.tax_status_updated')]);
    }

    public function export(Request $request): BinaryFileResponse
    {
            $data = [
            'data' => $this->getData($request)->get(),
            'search' => $request['search'] ?? null,
        ];

        if ($request['type'] == 'csv') {
            return Excel::download(new TaxVatExport($data), 'TaxList.csv');
        }
        return Excel::download(new TaxVatExport($data), 'TaxList.xlsx');
    }


    private function getData($request): object
    {
        $taxVats = $this->taxVat
        ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orWhere('name', 'LIKE', '%' . $key . '%')->orWhere('tax_rate', 'LIKE', '%' . $key . '%');
                }
            })
            ->when($this->getCountryType() == 'single', function ($query) {
                $query->where('is_default',true);
            },function ($query) use($request) {
                $query->where('country_code', $request->country_code);
            })
        ->latest()->select(['id', 'name', 'tax_rate', 'is_active']);
        return $taxVats;
    }
}
