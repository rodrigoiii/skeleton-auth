var JvBs3 = require("./helpers/JvBs3");

var ResetPassword = {
  init: function() {
    ResetPassword.initValidation();
  },

  initValidation: function() {
    var jvBs3 = new JvBs3("#reset-password-form", {
      errorElement: "span",
      errorClass: "help-block",

      rules: {
        new_password: {
          required: true,
          password_strength: true
        },
        confirm_new_password: {
          required: true,
          equalTo: '#reset-password-form :input[name="new_password"]'
        }
      },

      messages: {
        confirm_new_password: {
          equalTo: "Password and confirm password do not match"
        }
      }
    });

    jvBs3.validate();
  }
};

$(document).ready(ResetPassword.init);
