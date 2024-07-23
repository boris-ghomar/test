(function(jsGrid) {

    jsGrid.locales.fa = {
        grid: {
            noDataContent: "هیچ موردی یافت نشد",
            deleteConfirm: "آیا واقعاً می خواهید این مورد را حذف کنید؟",
            pagerFormat: "صفحات: {first} {prev} {pages} {next} {last} &nbsp;&nbsp; {pageIndex} از {pageCount}",
            pagePrevText: "قبلی",
            pageNextText: "بعدی",
            pageFirstText: "اولین",
            pageLastText: "آخرین",
            loadMessage: "لطفا صبر کنید ...",
            invalidMessage: "اطلاعات نامعتبر است !"
        },

        loadIndicator: {
            message: "در حال بارگذاری ..."
        },

        fields: {
            control: {
                searchModeButtonTooltip: "تغییر به حالت جستجو",
                insertModeButtonTooltip: "تغییر به حالت افزودن آیتم جدید",
                editButtonTooltip: "ویرایش",
                deleteButtonTooltip: "حذف",
                searchButtonTooltip: "جستجو",
                clearFilterButtonTooltip: "پاک کردن موارد فیلترینگ",
                insertButtonTooltip: "افزودن",
                updateButtonTooltip: "به روز رسانی",
                cancelEditButtonTooltip: "لغو به روزرسانی"
            }
        },

        validators: {
            required: { message: "ضروری" },
            rangeLength: { message: "طول مقدار فیلد خارج از محدوده تعریف شده است" },
            minLength: { message: "مقدار فیلد خیلی کوتاه است" },
            maxLength: { message: "مقدار فیلد خیلی طولانی است" },
            pattern: { message: "مقدار فیلد با الگوی تعریف شده مطابقت ندارد" },
            range: { message: "مقدار فیلد خارج از محدوده تعریف شده است" },
            min: { message: "مقدار فیلد خیلی کم است" },
            max: { message: "مقدار فیلد خیلی بزرگ است" }
        }
    };

}(jsGrid, jQuery));

