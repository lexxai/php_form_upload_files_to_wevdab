const registerForm = document.getElementById("registerForm");
registerForm.addEventListener("submit", (e) => {
  var hcaptchaVal = document.querySelector("[name=h-captcha-response]").value;
  if (hcaptchaVal === "") {
    e.preventDefault();
    alert("Please complete the hCaptcha");
  }
});
