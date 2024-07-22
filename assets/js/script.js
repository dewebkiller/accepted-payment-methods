
jQuery(document).ready(function($) {
    $(".nav-tab").click(function() {
        $(".nav-tab").removeClass("nav-tab-active");
        $(this).addClass("nav-tab-active");
        $(".tab-content").hide();
        var activeTab = $(this).attr("href");
        $(activeTab).show();
        return false;
    });

    $("#payment-methods-list").sortable();

    $("#add-payment-method").click(function() {
        var method = prompt("Enter the payment method name (e.g., discover):");
        if (method) {
            $("#payment-methods-list").append(
                `<li class="payment-method-item" data-method="${method}">
                    <img src="${apmData.plugin_url}assets/icons/${method}.svg" alt="${method}">
                    <span>${method.charAt(0).toUpperCase() + method.slice(1).replace('-', ' ')}</span>
                    <button class="remove-method">Remove</button>
                </li>`
            );
        }
    });

    $(document).on("click", ".remove-method", function() {
        $(this).closest(".payment-method-item").remove();
    });

    $("#save-payment-methods").click(function() {
        var methods = [];
        $("#payment-methods-list .payment-method-item").each(function() {
            methods.push($(this).data("method"));
        });

        $.post(apmData.ajax_url, {
            action: "save_payment_methods",
            methods: methods
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