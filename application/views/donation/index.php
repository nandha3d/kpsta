<!-- <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" /> -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>KPSTA | Donation</title>
        <link rel="icon" type="image/x-icon" href="<?php echo base_url(); ?>public/images/favicon.ico">
        <link rel="icon" sizes="192x192" href="<?php echo base_url(); ?>public/images/favicon.ico">
        <link rel="apple-touch-icon" href="<?php echo base_url(); ?>public/images/apple-touch-icon.png">
        
        <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/payment.css" />
  </head>
  <body>
    <main>
      <section class="payment">
        <div class="payment__wrapper">
          <div class="payment__content flex__item">
            <div class="logo">
              <img src="./public/images/payment/logo.png" alt="Logo" width="250px" />
            </div>
            <br />
            <br />
            <p class="mb-0">
              ഡിഎ ജീവനക്കാരന്റെ അവകാശമാണ്. അത് നേടിയെടുക്കുന്നതിനായുള്ള
              നിയമപോരാട്ടത്തില്‍ ഞാനും പങ്കാളിയായി.
              <br />
              <br />
              Thank you for joining the legal movement to achieve DA and its
              arrears.
              <br />
              <br />
              <br />
              With warm regard,
              <br />
              Kerala Pradesh School Teachers Association (KPSTA) State Committee
            </p>
          </div>
          <div class="flex__item">
            <div class="share">
              <a href="">
                <img
                  src="./public/images/payment/facebook.png"
                  alt="Facebook"
                  title="Share Facebook"
                  height="45"
                  width="45"
                />
              </a>
              <a href="">
                <img
                  src="./public/images/payment/whatsapp.png"
                  alt="Whatsapp"
                  title="Share Whatsapp"
                  height="45"
                  width="45"
                />
              </a>
              <a href="">
                <img
                  src="./public/images/payment/twitter.png"
                  alt="Twitter"
                  title="Share Twitter"
                  height="45"
                  width="45"
                />
              </a>
              <!-- <a href="">
                <img
                  src="./public/images/payment/instagram.png"
                  alt="Instagram"
                  title="Share Instagram"
                  height="45"
                  width="45"
                />
              </a>
              <a href="">
                <img
                  src="./public/images/payment/youtube.png"
                  alt="Youtube"
                  title="Share Youtube"
                  height="45"
                  width="45"
                /> -->
              </a>
            </div>
          </div>

          <!-- Payment Form -->
          <div class="payment__form flex__item">
            <p class="donation__text mt-0">
              We gratefully acknowledge your valuable contribution of &#8377;100
              towards meeting the expenses of this legal movement.
            </p>
            
           


            <?php echo $form ?>



          </div>
          <!-- Payment Form END-->

          <!-- Payment  Success and Certificate -->
          <!-- Remove the class "d-none" to show -->
          <div class="payment__form flex__item d__flex--column d-none">
            <p class="donation__text mt-0 bg__success self__align--center">
              Thank you for your valuable contribution of &#8377;100 towards
              meeting the expenses of this legal movement.
            </p>
            <div class="payment__certificate">
              <a href="#" title="Download Certificate">
                <img
                  src="./public/images/payment/certification.png"
                  alt="Download Certificate"
                  srcset=""
                />
                <div class="anchor__text">Download Certificate</div>
              </a>
            </div>
          </div>          
          <!-- Payment  Success and Certificate END -->
        </div>
      </section>
    </main>
 


 


<!-- <button id="rzp-button1">Pay</button> -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="<?php echo base_url(); ?>public/plugins/jQuery/jquery-2.2.3.min.js"></script>

<script>
var options = {
    "key": "<?=$key?>",  
    "currency": "INR",
    "name": "KPSTA",
    "description": "",
    "image": "<?= base_url("public/images/logo-xs.png")?>",
    "theme": {
        "color": "#3399cc"
    },
    'callback_url' : "<?= base_url('donation/payment-status')?>"
};
// var rzp1 = new Razorpay(options);
// rzp1.on('payment.failed', function (response){
//         alert(response.error.code);
//         alert(response.error.description);
//         alert(response.error.source);
//         alert(response.error.step);
//         alert(response.error.reason);
//         alert(response.error.metadata.order_id);
//         alert(response.error.metadata.payment_id);
// });

     $("body").on('submit', '#save', function (e) {
        $.ajax({
            url: $(this).data('href'),
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            context: this,
            success: function (result) {
                $(this).find('.modal-body .alert').remove();
                if (result.code == 'success') {
                    window.cookieStore.set("uuid", result.uuid)
                    options = {
                        ...options,
                        "amount": result?.amount,
                        "order_id": result?.orderId,
                        prefill: {
                            "name": result?.form?.name,
                            "email": result?.form?.email,
                            "contact": result?.form?.phone
                        }
                    }
                    var rzp1 = new Razorpay(options);
                    rzp1.on('payment.failed', function (response){
                      console.log(response)
                    })
                    rzp1.open();
                } else {
                    $(this).closest('form').replaceWith(result.form);
                }

            }
        });
        return false;
    });
</script>


</body>
</html>