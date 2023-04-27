window.language.zh_CN = {
    logout: "退出登录",
    success: "成功",
    failed: "失败",
    ok: "确定",
    cancel: "取消",
    type: {
        1: "虚拟货币",
        2: "支付宝",
        3: "微信",
    },
    menu: {
        dashboard: '仪表盘',
        account: '我的账号',
        order: '订单记录',
        settle: '结算管理',
        develop: '开发中心',
        contract: '签约支付',
        domain: '域名审核',
    },
    dashboard: {
        statistic: {
            title_1: "账户余额",
            title_2: "今日订单数",
            title_3: "今日收入",
            title_4: "待结算",
        },
        announcement: "公告",
        table: {
            custom_1: "收款货币",
            custom_2: "今日营收",
            custom_3: "代币",
            custom_4: "转换率",
        },
        statis: "统计"
    },
    record: {
        title: "订单记录",
        search: {
            order_sn: "任意订单号",
            token: "代币类型",
            status: "订单状态",
            created_at: "创建时间",
            bottom_1: "搜索",
            bottom_2: "重置"
        },
        table: {
            custom_1: "商家订单号",
            custom_2: "金额(CNY)",
            custom_3: "代币数量",
            custom_4: "代币名称",
            custom_5: "净额",
            custom_6: "订单状态",
            custom_7: "通知状态",
            custom_8: "创建时间",
            custom_9: "操作",
            custom_10: "支付订单号",
            custom_11: "回调订单号",
            custom_12: "回调时间",
            custom_13: "代币地址",
            custom_14: "回调地址",
            custom_15: "支付场景",
            status_1: "通知成功",
            status_2: "通知失败",
            status_3: "待通知",
            status_4: "待支付",
            status_5: "已过期",
            status_6: "已支付",
            status_7: "已完成",
            status_8: "已退款",
            bottom_1: "详情",
            bottom_2: "补单",
            bottom_3: "退款",
            platform_1: '手机',
            platform_2: '电脑'
        },
        modal: {
            title: "订单详情",
            footer_bottom: "取消"
        },
        confirm: {
            title: "警告",
            content: "确定是否向未付款订单发起通知请求？"
        },
        confirm2: {
            title: "警告",
            content: "确定是否退款？"
        }
    },
    settle: {
        title: "结算管理",
        table: {
            custom_0: "结算单号",
            custom_1: "结算金额(CNY)",
            custom_2: "USDT金额",
            custom_3: "结算方式",
            custom_4: "结算地址",
            custom_5: "USDT汇率",
            custom_6: "结算时间",
            custom_8: "状态",
            custom_9: "操作",
            bottom_1: "查看",
            status: {
                0 : '待结算',
                1 : '已结算',
            }
        }
    },
    develop: {
        title: "密钥信息",
        text_1: "接口地址",
        interface_document: "接口文档",
        text_2: "密钥仅会在重置时显示一次，如果忘记了密钥请点击重置密钥，重置后之前的密钥将无法使用。",
        bottom_1: "重置密钥",
        card: {
            title: "使用教程",
        },
        model: {
            title: "请注意",
            content: "重置密钥后当前密钥将会失效影响当前业务，请确认是否重置？",
            text: "请复制并保存您的新密钥",
        }
    },
    contract: {
        title: "签约支付",
        text_1: "到期时间",
        button_1: "签约",
        button_2: "已签约",
        button_3: "更换地址",
        model: {
            title: "订单确定",
            title_2: "修改地址",
            title_3: "签约支付",
            text_1: "签约支付",
            text_2: "签约类型",
            text_3: "代币种类",
            text_4: "签约周期",
            text_5: "支付方式",
            text_6: "支付价格",
            text_7: "代币地址",
            alert_1: "请务必核对地址是否正确，如果因填写错误导致转账失败概不负责且无法追回。",
            placeholder: "请输入当前币种收款的代币地址",
            placeholder_2: "到账金额需要与下方显示的金额一致，否则系統无法确认！<br>请及时转账，如果未在规定时间内到账，订单将会自动取消！",
            month_price: "单月",
            quarter_price: "季度",
            half_year_price: "半年",
            year_price: "一年(推荐)",
            three_year_price: "三年",
        }
    },
    account: {
        title_1: "结算管理",
        title_2: "账户管理",
        alert_1: "请务必核对结算地址是否正确，如果因<span style='color: #1890ff'>填写错误</span>导致转账失败<span style='color: red'>概不负责</span>且<span style='color: red'>无法追回</span>。",
        placeholder: "请输入USDT-TRC20收款的代币地址",
        usdt_description: "泰达币(USDT)是最受欢迎的稳定币之一。",
        text_1: "修改密码",
        description_1: "定期对账户密码进行修改保证账户安全",
        text_2: "绑定Telegram",
        description_2: "绑定 Telegram 接收收款通知和公告",
        text_3: "删除账户",
        description_3: "删除账户所有信息，此操作不可逆",
        button_1: "修改",
        button_2: "绑定",
        button_3: "删除",
        button_4: "已绑定",
        modal_1: {
            title: "修改密码",
            label_1: "旧密码",
            label_2: "新密码",
            label_3: "确认新密码",
            error: "请输入密码~",
        },
        modal_2: {
            title: "绑定Telegram机器人",
            button_1: "一键绑定Telegram",
            text_1: "一键绑定过程中如遇到问题, 可尝试手动绑定",
            text_2: "1. 在 Telegram 添加机器人账号 <a>@{telegram_bot}</a>",
            text_3: "2. 发送命令 <a>/bind {app_id}</a> 给机器人",
        },
        modal_3: {
            title: "删除确定",
            content: "注意：您的所有数据都会被永久删除，并且无法找回。请谨慎操作！请谨慎操作！请谨慎操作！"
        }
    },
    charges: {
        card: {
            title: "订单详情",
            label_1: "支付金额",
            label_2: "创建时间",
            label_3: "支付方式",
            alert_1: "到账金额需要与下方显示的金额一致，否则系統无法确认！<br>请及时转账，如果未在规定时间内到账，订单将会自动取消！",
            button_1: "点击支付",
            button_2: "选择其他支付",
        },
        tip: "若您需要加密货币，可以去以下交易所购买虚拟货币",
    },
    domain: {
        title: "域名审核",
        table: {
            custom_1: "域名",
            custom_2: "审核状态",
            custom_3: "创建时间",
            custom_4: "操作",
            button: "删除",
        },
        modal: {
            title: "新增域名",
            placeholder: "请输入新增域名，不要带http和/",
            span: "请输入准确的二级域名，例子: www.baidu.com",
            submit: "提交",
            cancel: "取消",
            submit_empty_domain: '请输入域名'
        }
    },
}
