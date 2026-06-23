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
    var mediaUploader;
    $("#upload-icon-button").click(function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: "Upload Payment Method Icon",
            button: {
                text: "Use this icon"
            },
            multiple: false
        });

        mediaUploader.on("select", function() {
            var attachment = mediaUploader.state().get("selection").first().toJSON();
            $("#new-payment-method").val(attachment.url).trigger("change");
        });

        mediaUploader.open();
    });

    // Handle changes to the payment method input to update the preview dynamically if it's a URL
    $("#new-payment-method").on("input change", function() {
        var val = $(this).val();
        if (val && (val.startsWith('http://') || val.startsWith('https://') || val.startsWith('/') || val.indexOf('.') > -1)) {
            $("#upload-icon-preview img").attr("src", val);
            $("#upload-icon-preview").css("display", "flex");
        } else {
            $("#upload-icon-preview img").attr("src", "");
            $("#upload-icon-preview").hide();
        }
    });

    // Handle removal of uploaded preview icon
    $("#remove-preview-icon").click(function() {
        $("#new-payment-method").val("").trigger("change");
    });

    // Handle form submission for adding new payment method
    $("#add-payment-method-form").submit(function(e) {
        e.preventDefault();
        var val = $("#new-payment-method").val();
        var nonce = apmData.nonce;

        if (!val) {
            alert(apmData.msg_empty_method);
            return;
        }

        var method = "";
        var icon = "";

        // Check if the value is a URL
        if (val.startsWith('http://') || val.startsWith('https://') || val.startsWith('/') || val.indexOf('.') > -1) {
            icon = val;
            // Extract filename without extension as method name
            var filename = val.substring(val.lastIndexOf('/') + 1);
            method = filename.substring(0, filename.lastIndexOf('.')) || filename;
        } else {
            method = val;
        }

        if (!method || !icon) {
            alert(apmData.msg_upload_icon);
            return;
        }

        $.ajax({
            url: apmData.ajax_url,
            type: "POST",
            data: {
                action: "add_payment_method",
                method: method,
                icon: icon,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    $("#payment-methods-list").append(
                        `<li class="payment-method-item" data-method="${method}">
                            <img src="${icon}" alt="${method}">
                            <span>${method.charAt(0).toUpperCase() + method.slice(1).replace(/-/g, ' ')}</span>
                            <button class="remove-method">Remove</button>
                        </li>`
                    );
                    $("#new-payment-method").val("").trigger("change");
                } else {
                    alert(apmData.msg_error_add);
                }
            }
        });
    });

    // Handle toggle between Manual Upload and Pre-built Library
    $("input[name='dwk_apm_source']").change(function() {
        var source = $(this).val();
        if (source === 'manual') {
            $("#manual-upload-container").show();
            $("#prebuilt-library-container").hide();
            $("#tab-1").removeClass("full-width");
        } else {
            $("#manual-upload-container").hide();
            $("#prebuilt-library-container").show();
            $("#tab-1").addClass("full-width");
        }
    });

    // Trigger initial toggle state on page load
    $("input[name='dwk_apm_source']:checked").trigger("change");

    // Style pre-built checkbox items when checked
    $(".prebuilt-checkbox").change(function() {
        if ($(this).is(":checked")) {
            $(this).parent().css({
                "border-color": "var(--colorbrand)",
                "background-color": "rgba(29, 139, 221, 0.05)"
            });
        } else {
            $(this).parent().css({
                "border-color": "#e5e5e5",
                "background-color": "#fff"
            });
        }
    }).trigger("change");

    // Handle removal of payment method
    $(document).on("click", ".remove-method", function() {
        $(this).closest(".payment-method-item").remove();
    });

    // Handle saving payment methods
    $("#save-payment-methods").click(function() {
        var source = $("input[name='dwk_apm_source']:checked").val();
        var paymentMethods = {};
        var checkedLibrary = [];

        if (source === 'manual') {
            $("#payment-methods-list .payment-method-item").each(function() {
                var method = $(this).data("method");
                var image = $(this).find('img').prop('src');
                paymentMethods[method] = image;
            });
        } else {
            $(".prebuilt-checkbox:checked").each(function() {
                checkedLibrary.push($(this).val());
            });
        }
       
        $.post(apmData.ajax_url, {
            action: "save_payment_methods",
            source: source,
            methods: paymentMethods,
            checked_library: checkedLibrary,
            nonce: apmData.nonce
        }, function(response) {
            var message = $("#apm-save-message");
            if (response.success) {
                message.removeClass("error").addClass("success").text(apmData.msg_save_success).show();
            } else {
                message.removeClass("success").addClass("error").text(apmData.msg_save_error).show();
            }
            setTimeout(function() {
                message.hide();
            }, 3000);
        });
    });
});