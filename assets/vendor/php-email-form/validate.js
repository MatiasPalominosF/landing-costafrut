/**
 * PHP Email Form Validation - v3.1
 * URL: https://bootstrapmade.com/php-email-form/
 * Author: BootstrapMade.com
 */
(function () {
  "use strict";

  let forms = document.querySelectorAll(".php-email-form");

  forms.forEach(function (e) {
    e.addEventListener("submit", function (event) {
      event.preventDefault();

      let thisForm = this;

      let action = thisForm.getAttribute("action");
      let recaptcha = thisForm.getAttribute("data-recaptcha-site-key");

      if (!action) {
        displayError(thisForm, "The form action property is not set!");
        return;
      }

      console.log(document.getElementById("submitBtn"));
      thisForm.querySelector(".loading").classList.add("d-block");
      document.getElementById("submitBtn").disabled = true;
      thisForm.querySelector(".error-message").classList.remove("d-block");
      thisForm.querySelector(".sent-message").classList.remove("d-block");

      let formData = new FormData(thisForm);

      if (recaptcha) {
        if (typeof grecaptcha !== "undefined") {
          grecaptcha.ready(function () {
            try {
              grecaptcha
                .execute(recaptcha, { action: "php_email_form_submit" })
                .then((token) => {
                  php_email_form_submit(thisForm, action, formData);
                });
            } catch (error) {
              displayError(thisForm, error);
            }
          });
        } else {
          displayError(
            thisForm,
            "The reCaptcha javascript API url is not loaded!"
          );
        }
      } else {
        php_email_form_submit(thisForm, action, formData);
      }
    });
  });

  function php_email_form_submit(thisForm, action, formData) {
    let data = {
      name: formData.get("name"),
      email: formData.get("email"),
      subject: formData.get("subject"),
      message: formData.get("message"),
    };

    console.log("action: ", action);
    console.log("data: ", data);

    $.ajax({
      url: "https://agricola.socialtalk.cl/" + action,
      data: data,
      type: "POST",
      success: function (data) {
        console.log("Data success: ", data);
        thisForm.querySelector(".loading").classList.remove("d-block");

        if (!data.error) {
          thisForm.querySelector(".sent-message").classList.add("d-block");
          var count = 0;
          var interval = setInterval(() => {
            count += 1;
            if (count === 5) {
              thisForm
                .querySelector(".sent-message")
                .classList.remove("d-block");
              clearInterval(interval);
            }
          }, 1000);
          thisForm.reset();
          document.getElementById("submitBtn").disabled = false;
        } else {
          displayError(
            thisForm,
            "Error al enviar el correo. Inténtelo nuevamente."
          );
        }
      },
      error: function (jqXHR, exception) {
        console.log(jqXHR, ":a");
        var msg = "";
        if (jqXHR.status === 0) {
          msg = "Not connect.\n Verify Network.";
        } else if (jqXHR.status == 404) {
          msg = "Requested page not found. [404]";
        } else if (jqXHR.status == 500) {
          msg = "Internal Server Error [500].";
        } else if (exception === "parsererror") {
          msg = "Requested JSON parse failed.";
        } else if (exception === "timeout") {
          msg = "Time out error.";
        } else if (exception === "abort") {
          msg = "Ajax request aborted.";
        } else {
          msg = "Uncaught Error.\n" + jqXHR.responseText;
        }
        displayError(thisForm, msg);
        document.getElementById("submitBtn").disabled = false;
      },
      complete: function (a) {
        // Handle the complete event
        console.log("ajax completed ", a);
        document.getElementById("submitBtn").disabled = false;
      },
    });

    // fetch(action, {
    //   method: "POST",
    //   body: formData,
    //   headers: {
    //     "X-Requested-With": "XMLHttpRequest",
    //   },
    // })
    //   .then((response) => {
    //     if (response.ok) {
    //       return response.text();
    //     } else {
    //       throw new Error(
    //         `${response.status} ${response.statusText} ${response.url}`
    //       );
    //     }
    //   })
    //   .then((data) => {
    //     console.log("DATA: ", data);
    //     thisForm.querySelector(".loading").classList.remove("d-block");
    //     if (data.trim() == "OK") {
    //       thisForm.querySelector(".sent-message").classList.add("d-block");
    //       var count = 0;
    //       var interval = setInterval(() => {
    //         count += 1;
    //         if (count === 5) {
    //           thisForm
    //             .querySelector(".sent-message")
    //             .classList.remove("d-block");
    //           clearInterval(interval);
    //         }
    //       }, 1000);
    //       thisForm.reset();
    //     } else {
    //       console.log("Entró acá");
    //       throw new Error(
    //         data
    //           ? data
    //           : "Form submission failed and no error message returned from: " +
    //             action
    //       );
    //     }
    //   })
    //   .catch((error) => {
    //     displayError(thisForm, error);
    //   });
  }

  function displayError(thisForm, error) {
    thisForm.querySelector(".loading").classList.remove("d-block");
    thisForm.querySelector(".error-message").innerHTML = error;
    thisForm.querySelector(".error-message").classList.add("d-block");
  }
})();
