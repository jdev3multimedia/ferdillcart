<html>
   <head>
      <title>Show Payment Page</title>
   </head>
   <body>
      <center>
         <h1>Please do not refresh this page...</h1>
      </center>
      <form method="post" action="{{ Config::get('paytm_config.PAYTM_STATUS_QUERY_NEW_URL') }}?mid={{ Config::get('paytm_config.PAYTM_MERCHANT_MID') }}&orderId={{ $ORDER_ID }}" name="paytm">
         <table border="1">
            <tbody>
               <input type="hidden" name="mid" value="{{ Config::get('paytm_config.PAYTM_MERCHANT_MID') }}">
               <input type="hidden" name="orderId" value="{{ $ORDER_ID }}">
               <input type="hidden" name="txnToken" value="{{ $txnToken }}">
            </tbody>
         </table>
         <script type="text/javascript"> document.paytm.submit(); </script>
      </form>
   </body>
</html>
