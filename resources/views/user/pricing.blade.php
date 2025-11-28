<x-layouts.app :title="$title ?? 'Pricing Plans'">


       <!--TEST-->

<script async src="https://js.stripe.com/v3/pricing-table.js"></script>
<stripe-pricing-table pricing-table-id="prctbl_1STdoCDfpo67wO4dX6Kr1bDX"
publishable-key="pk_test_51Ngsx6Dfpo67wO4dIywhaxJ5DczHuc6BZdgestz0BZHLcG85vD3QpmNYWEYmTmD2qwMMj0UEonJnwdcoiY6J6mHg006USXIY5Y">
</stripe-pricing-table>

<!--LIVE -->
   
 <!--<script async src="https://js.stripe.com/v3/pricing-table.js"></script>-->
 <!--   <stripe-pricing-table pricing-table-id="prctbl_1STdVTDfpo67wO4diuVN6NRA"-->
 <!--   publishable-key="pk_live_51Ngsx6Dfpo67wO4dGZRpVZ1UpDMKFoaY1tFikV4ToYcbvkSBxzb2bNpDqyuvrjADJuJ18AbdZMbVIv1Fm4ufsEqr00iCQyBPdH">-->
 <!--   </stripe-pricing-table>-->

     

  </div>

</x-layouts.app>
