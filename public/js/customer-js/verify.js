document.addEventListener("DOMContentLoaded", function () {
  var form       = document.getElementById("verifyForm");
  var codeInput  = document.getElementById("code");
  var emailShown = document.getElementById("emailShown");
  var errorBox   = document.getElementById("formError");
  var successBox = document.getElementById("formSuccess");
  var verifyBtn  = document.getElementById("verifyBtn");
  var resendBtn  = document.getElementById("resendBtn");

  // Read email from URL query param or sessionStorage
  var urlParams = new URLSearchParams(window.location.search);
  var email = urlParams.get("email") || sessionStorage.getItem("pendingEmail");

  if (!email) {
    window.location.href = (window.URLROOT || "") + "/customer/register";
    return;
  }

  sessionStorage.setItem("pendingEmail", email);
  if (emailShown) emailShown.textContent = email;

  /* Helper functions */
  function showError(msg) {
    if (successBox) { successBox.hidden = true; successBox.textContent = ""; }
    if (errorBox) { errorBox.textContent = msg; errorBox.hidden = false; }
  }

  function showSuccess(msg) {
    if (errorBox) { errorBox.hidden = true; errorBox.textContent = ""; }
    if (successBox) { successBox.textContent = msg; successBox.hidden = false; }
  }

  function postApi(endpoint, data) {
    var url = (window.URLROOT || "") + "/customer/" + endpoint;
    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(Object.assign({ email: email }, data))
    }).then(function (res) {
      return res.json();
    });
  }

  // Allow numeric input only
  if (codeInput) {
    codeInput.addEventListener("input", function () {
      codeInput.value = codeInput.value.replace(/\D/g, "").slice(0, 6);
      if (errorBox) errorBox.hidden = true;
    });
  }

  // Handle form submission
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      var code = codeInput ? codeInput.value.trim() : "";
      if (code.length !== 6) {
        showError("Please enter the 6-digit verification code.");
        return;
      }

      if (verifyBtn) verifyBtn.disabled = true;

      postApi("apiVerify", { code: code })
        .then(function (res) {
          if (res.ok) {
            showSuccess(res.message || "Email verified! Redirecting...");
            sessionStorage.removeItem("pendingEmail");
            setTimeout(function () {
              window.location.href = res.redirect || ((window.URLROOT || "") + "/customer/login?verified=1");
            }, 1500);
          } else {
            showError(res.message || "Verification failed. Please try again.");
            if (verifyBtn) verifyBtn.disabled = false;
          }
        })
        .catch(function () {
          showError("Could not reach the server. Please check your connection.");
          if (verifyBtn) verifyBtn.disabled = false;
        });
    });
  }

  // Handle resend code with 60s cooldown timer
  var cooldownTimer = null;

  function startCooldown(seconds) {
    var left = seconds;
    resendBtn.disabled = true;
    resendBtn.textContent = "Resend code (" + left + "s)";

    clearInterval(cooldownTimer);
    cooldownTimer = setInterval(function () {
      left--;
      if (left <= 0) {
        clearInterval(cooldownTimer);
        resendBtn.disabled = false;
        resendBtn.textContent = "Resend code";
      } else {
        resendBtn.textContent = "Resend code (" + left + "s)";
      }
    }, 1000);
  }

  if (resendBtn) {
    resendBtn.addEventListener("click", function () {
      resendBtn.disabled = true;

      postApi("apiResendCode", {})
        .then(function (res) {
          if (res.ok) {
            showSuccess("A new code has been sent. Please check your inbox.");
            startCooldown(60);
          } else {
            showError(res.message || "Could not resend code.");
            resendBtn.disabled = false;
          }
        })
        .catch(function () {
          showError("Could not reach the server. Please try again.");
          resendBtn.disabled = false;
        });
    });
  }
});
