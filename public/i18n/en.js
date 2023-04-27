window.language.en = {
    logout: "Logout",
    success: "Success",
    failed: "Failed",
    ok: "OK",
    cancel: "Cancel",
    type: {
        1: "Virtual Currency",
        2: "Alipay",
        3: "WeChat",
    },
    menu: {
        dashboard: 'Dashboard',
        account: 'Account',
        order: 'Order',
        settle: 'Settlement',
        develop: 'Development',
        contract: 'Contract',
        domain: 'Domain'
    },
    dashboard: {
        statistic: {
            title_1: "Account Balance",
            title_2: "Today's Orders",
            title_3: "Today's Income",
            title_4: "Pending settlement",
        },
        announcement: "Announcement",
        table: {
            custom_1: "Receipt Currency",
            custom_2: "Today's Revenue",
            custom_3: "Token",
            custom_4: "Conversion Rate",
        },
        statis: "stats"
    },
    record: {
        title: "Order Record",
        search: {
            order_sn: "Any order number",
            token: "Token Type",
            status: "Order Status",
            created_at: "Create time",
            bottom_1: "Search",
            bottom_2: "Reset"
        },
        table: {
            custom_1: "Merchant order number",
            custom_2: "Amount(CNY)",
            custom_3: "Number of tokens",
            custom_4: "Token Name",
            custom_5: "Net Amount",
            custom_6: "Order Status",
            custom_7: "Notification Status",
            custom_8: "Create time",
            custom_9: "Operation",
            custom_10: "Payment order number",
            custom_11: "Callback order number",
            custom_12: "Callback time",
            custom_13: "Token address",
            custom_14: "Callback address",
            custom_15: "Payment Scene",
            status_1: "Notification successful",
            status_2: "Notification failed",
            status_3: "To be notified",
            status_4: "Pending payment",
            status_5: "Expired",
            status_6: "paid",
            status_7: "Completed",
            status_8: "Refunded",
            bottom_1: "Details",
            bottom_2: "Replenish order",
            bottom_3: "Refund",
            platform_1: 'Mobile',
            platform_2: 'PC'
        },
        modal: {
            title: "Order Details",
            footer_bottom: "Cancel"
        },
        confirm: {
            title: "Warning",
            content: "Are you sure whether to initiate a notification request for unpaid orders?"
        },
        confirm2: {
            title: "Warning",
            content: "Are you sure you want to refund?"
        }
    },
    settle: {
        title: "Billing Management",
        table: {
            custom_0: "number",
            custom_1: "Amount(CNY)",
            custom_2: "USDT",
            custom_3: "Method",
            custom_4: "Address",
            custom_5: "Rate",
            custom_6: "Time",
            custom_8: "Status",
            custom_9: "Operation",
            bottom_1: "View",
            status: {
                0 : 'Settled',
                1 : 'Success',
            }
        }
    },
    develop: {
        title: "Key Information",
        text_1: "Interface address",
        interface_document: "Interface Document",
        text_2: "The key will only be displayed once when you reset it. If you forget the key, please click to reset the key. After the reset, the previous key will not be available.",
        bottom_1: "Reset key",
        card: {
            title: "Using Tutorial",
        },
        model: {
            title: "Please note",
            content: "After resetting the key, the current key will become invalid and affect the current business. Please confirm whether to reset?",
            text: "Please copy and save your new key",
        }
    },
    contract: {
        title: "Contract payment",
        text_1: "Expiration Time",
        button_1: "Sign up",
        button_2: "Contracted",
        button_3: "Change address",
        model: {
            title: "Order Confirmed",
            title_2: "Modify address",
            title_3: "Contract payment",
            text_1: "Contract payment",
            text_2: "Contract Type",
            text_3: "Token Type",
            text_4: "Signing Cycle",
            text_5: "Payment method",
            text_6: "Pay Price",
            text_7: "Token address",
            alert_1: "Please be sure to check whether the address is correct. If the transfer fails due to an error, we will not be responsible and cannot be recovered.",
            placeholder: "Please enter the token address of the current currency to receive payment",
            placeholder_2: "The amount received must be the same as the amount shown below, otherwise the system will not be able to confirm it!<br>Please transfer money in time, if it is not received within the specified time, the order will be automatically cancelled!",
            month_price: "Monthly",
            quarter_price: "Quarter",
            half_year_price: "Half Year",
            year_price: "one year (recommended)",
            three_year_price: "Three years",
        }
    },
    account: {
        title_1: "Billing Management",
        title_2: "Account Management",
        alert_1: "Please be sure to check whether the settlement address is correct. If the transfer fails due to <span style='color: #1890ff'>filling error</span><span style='color: red'>is not responsible</span> And <span style='color: red'>cannot be recovered</span>.",
        placeholder: "Please enter the token address for receiving USDT-TRC20",
        usdt_description: "Tether (USDT) is one of the most popular stablecoins.",
        text_1: "Change password",
        description_1: "Regularly modify the account password to ensure account security",
        text_2: "Bind Telegram",
        description_2: "Bind Telegram to receive payment notifications and announcements",
        text_3: "Delete account",
        description_3: "Delete all account information, this operation is irreversible",
        button_1: "Modify",
        button_2: "Bind",
        button_3: "Delete",
        button_4: "Binding",
        modal_1: {
            title: "Change Password",
            label_1: "Old password",
            label_2: "New password",
            label_3: "Confirm new password",
            error: "Please enter your password~",
        },
        modal_2: {
            title: "Bind Telegram Robot",
            button_1: "One key binding Telegram",
            text_1: "If you encounter problems during the one-key binding process, you can try to bind manually",
            text_2: "1. Add a bot account in Telegram <a>@{telegram_bot}</a>",
            text_3: "2. Send the command <a>/bind {app_id}</a> to the robot",
        },
        modal_3: {
            title: "Delete OK",
            content: "Attention: All your data will be permanently deleted and cannot be retrieved. Please operate with caution! Please operate with caution! Please operate with caution!"
        }
    },
    charges: {
        card: {
            title: "Order Details",
            label_1: "Payment Amount",
            label_2: "Create time",
            label_3: "Payment Method",
            alert_1: "The amount received must be the same as the amount shown below, otherwise the system will not be able to confirm it!<br>Please transfer money in time, if it is not received within the specified time, the order will be automatically cancelled!",
            button_1: "Click to pay",
            button_2: "Select another payment",
        },
        tip: "If you need cryptocurrency, you can go to the following exchanges to buy virtual currency",
    },
    domain: {
        title: "Domain",
        table: {
            custom_1: "Domain",
            custom_2: "AuditStatus",
            custom_3: "CreateTime",
            custom_4: "Operation",
            button: "Delete",
        },
        modal: {
            title: "New Domain",
            placeholder: "Please enter the new domain name,don't http and /",
            span: "Please enter the correct second-level domain name,illustrate: www.baidu.com",
            submit: "submit",
            cancel: "cancel",
            submit_empty_domain: 'Please enter domain name'
        }
    },
}
