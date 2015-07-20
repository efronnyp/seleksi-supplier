/*
 *
 * Copyright (c) 2015 by Lewi Hussey (http://codepen.io/Lewitje/pen/BNNJjo)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files
 * (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 **/

/*
 * Modified by Efronny Pardede (Inferno Technology Community)
 **/

$("#login-button").click(function(event){
	 event.preventDefault();

	 var error = '<label for="error_message"></label>';
	 var uname = $("input[name='uname']");
	 var pwd = $("input[name='password']");
	 
	 if (!uname.val()) {
		 uname.parent("div").addClass("has-error").prepend(error);
		 uname.prev("label").html("Mohon masukkan username anda");
		 return false;
	 } else if (!pwd.val()) {
		 pwd.parent("div").addClass("has-error").prepend(error);
		 pwd.prev("label").html("Mohon masukkan password anda");
		 return false;
	 }
	 
	 $.post("/auth/submit", { username:  uname.val(), password: pwd.val() }, function (data) {
		 var result = JSON.parse(data);
		 
		 if (result.success) {
			 $('form').fadeOut(500);
			 $('.wrapper').addClass('form-success');
			 window.setTimeout(function(){
				 window.location.replace("/");
			 }, 3000);
		 } else {
			 if (result.errorType === 'username') {
				 pwd.val("");
				 uname.parent("div").addClass("has-error").prepend(error);
				 uname.prev("label").html(result.errorMessage);
			 } else if (result.errorType === 'password') {
				 pwd.parent("div").addClass("has-error").prepend(error);
				 pwd.prev("label").html(result.errorMessage);
			 } else {
				 alert(result.errorMessage);
			 }
		 }
	 });
});

$("input").blur(function(){
	 if ($(this).parent("div").hasClass("has-error")) {
		 $(this).parent("div").removeClass("has-error");
		 $(this).prev("label").detach().remove();
	 }
});

$(".alert").fadeOut(2000);