jQuery(document).ready(function($) {
    // Switch between tabs
    $(".nav-tab").click(function() {
        $(".nav-tab").removeClass("nav-tab-active");
        $(this).addClass("nav-tab-active");
        $(".tab-content").hide();
        var activeTab = $(this).attr("href");
        $(activeTab).show();
        return false;
    });

    // Make payment methods list sortable
    $("#payment-methods-list").sortable();

    // Handle media uploader
    $("#upload-icon-button").click(function(e) {
        e.preventDefault();

        var mediaUploader = wp.media({
            title: "Upload Payment Method Icon",
            button: {
                text: "Use this icon"
            },
            multiple: false
        });

        mediaUploader.on("select", function() {
            var attachment = mediaUploader.state().get("selection").first().toJSON();
            $("#upload-icon").val(attachment.url); // Set the icon URL as the value of the hidden input field
            console.log(attachment);
        });

        mediaUploader.open();
    });

    // Handle form submission for adding new payment method
    $("#add-payment-method-form").submit(function(e) {
        e.preventDefault();
        var method = $("#new-payment-method").val();
        var icon = $("#upload-icon").val(); // Get the icon URL from the hidden input field
        var nonce = apmData.nonce; // Retrieve nonce from apmData

        if (!method || !icon) {
            alert("Please enter payment method and upload an icon.");
            return;
        }

        $.ajax({
            url: apmData.ajax_url,
            type: "POST",
            data: {
                action: "add_payment_method",
                method: method,
                icon: icon,
                nonce: nonce // Include nonce in the AJAX request
            },
            success: function(response) {
                if (response.success) {
                    $("#payment-methods-list").append(
                        `<li class="payment-method-item" data-method="${method}">
                            <img src="${icon}" alt="${method}">
                            <span>${method.charAt(0).toUpperCase() + method.slice(1).replace('-', ' ')}</span>
                            <button class="remove-method">Remove</button>
                        </li>`
                    );
                    $("#new-payment-method").val("");
                    $("#upload-icon").val("");
                } else {
                    alert("Error adding payment method.");
                }
            },
            error: function(xhr, status, error) {
                console.log('status', status);
                console.log('error', error);
                console.log('xhr', xhr);
            }
        });
    });

    // Handle removal of payment method
    $(document).on("click", ".remove-method", function() {
        $(this).closest(".payment-method-item").remove();
    });

    // Handle saving payment methods
    $("#save-payment-methods").click(function() {
        var paymentMethods = {};
        $("#payment-methods-list .payment-method-item").each(function() {
            var method = $(this).data("method");
            var image = $(this).find('img').prop('src');
            
            paymentMethods[method] = image;
        });
       
      // console.log(paymentMethods);
        $.post(apmData.ajax_url, {
            action: "save_payment_methods",
            methods: paymentMethods,
            nonce: apmData.nonce
        }, function(response) {
            var message = $("#apm-save-message");
            if (response.success) {
                message.removeClass("error").addClass("success").text("Payment methods saved successfully!").show();
            } else {
                message.removeClass("success").addClass("error").text("Error saving payment methods.").show();
            }
            setTimeout(function() {
                message.hide();
            }, 3000);
        });
    });
});