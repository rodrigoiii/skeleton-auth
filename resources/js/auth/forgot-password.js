var JvBs3 = require("./helpers/JvBs3");

var ForgotPassword = {
  init: function() {
    ForgotPassword.initValidation();
  },

  initValidation: function() {
    var jvBs3 = new JvBs3("#forgot-password-form", {
      errorElement: "span",
      errorClass: "help-block",

      rules: {
        email: {
          required: true,
          email: true,
          remote: "/api/jv/email-exist"
        }
      },

      messages: {
        email: {
          remote: "Email is not exist!"
        }
      }
    });

    jvBs3.validate();
  }
};

$(document).ready(ForgotPassword.init);
