var JvBs3 = require("./helpers/JvBs3");

var Register = {
  init: function() {
    Register.initValidation();
  },

  initValidation: function() {
    var jvBs3 = new JvBs3("#register-form", {
      errorElement: "span",
      errorClass: "help-block",

      rules: {
        picture: {
          required: true,
          accept: "image/gif,image/jpeg,image/png",
          file_size: {
            max_size: 5000000, // 5mb
          }
        },
        first_name: {
          required: true,
          regex: /^[a-zA-Z\s]+$/i
        },
        last_name: {
          required: true,
          regex: /^[a-zA-Z\s]+$/i
        },
        email: {
          required: true,
          email: true,
          remote: "/api/jv/email-exist?invert"
        },
        password: {
          required: true,
          password_strength: true
        },
        confirm_password: {
          required: true,
          equalTo: '#register-form :input[name="password"]'
        }
      },

      messages: {
        first_name: {
          regex: "Please enter only letters."
        },
        last_name: {
          regex: "Please enter only letters."
        },
        email: {
          remote: "Email is already taken."
        },
        confirm_password: {
          equalTo: "Password and confirm password do not match"
        }
      }
    });

    jvBs3.validate();
  }
};

$(document).ready(Register.init);
