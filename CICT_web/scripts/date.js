mobiscroll.setOptions({
    theme: 'ios',
    themeVariant: 'light'
});

var formatDate = mobiscroll.util.datetime.formatDate;
var startDate = '2023-05-08T00:00';
var endDate = '2023-05-14T00:00';
var dateInput = document.getElementById('date-filtering');
var startInput = document.getElementById('date-filtering-start');
var endInput = document.getElementById('date-filtering-end');

var now = new Date();
var day = now.getDay();
var monday = now.getDate() - day + (day === 0 ? -6 : 1);

var select = mobiscroll.select('#date-filtering-select', {
    inputElement: document.getElementById('date-select-input'),
    responsive: {
        xsmall: {
            touchUi: true
        },
        small: {
            touchUi: false
        }
    },
    onChange: function (event) {
        var selected = event.value;

        if (selected === 'custom') {
            disableInputs(false);
        } else {
            disableInputs(true);

            switch (selected) {
                case 'today':
                    calendar.setVal(['2023-05-08T00:00', '2023-05-08T00:00']);
                    break;
                case 'yesterday':
                    calendar.setVal(['2023-05-07T00:00', '2023-05-07T00:00']);
                    break;
                case 'last-week':
                    calendar.setVal([new Date(now.getFullYear(), now.getMonth(), monday - 7), new Date(now.getFullYear(), now.getMonth(), monday - 1)]);
                    break;
                case 'last-month':
                    calendar.setVal(['2023-04-01T00:00', '2023-04-30T00:00']);
                    break;
                case 'last-7-days':
                    calendar.setVal(['2023-05-02T00:00', '2023-05-08T00:00']);
                    break;
                case 'last-30-days':
                    calendar.setVal(['2023-04-09T00:00', '2023-05-08T00:00']);
                    break;
            }
        }
    }
});

function disableInputs(disable) {
    var startInst = mobiscroll.getInst(startInput);
    var endInst = mobiscroll.getInst(endInput);

    startInst.setOptions({ disabled: disable });
    endInst.setOptions({ disabled: disable });
}

var calendar = mobiscroll.datepicker('#date-filtering-calendar', {
    controls: ['calendar'],
    select: 'range',
    display: 'inline',
    showRangeLabels: false,
    pages: 'auto',
    startInput: '#date-filtering-start',
    endInput: '#date-filtering-end',
    returnFormat: 'iso8601',
    showOnClick: false,
    showOnFocus: false,
    onInit: function (event, inst) {
        inst.setVal([startDate, endDate]);
        setInputValue(startDate, endDate, inst.s.dateFormat)
    },
    onChange: function () {
        disableInputs(false);
        select.setVal('custom');
    }
});

var popup = mobiscroll.popup('#date-filtering-popup', {
    responsive: {
        xsmall: {
            display: 'bottom',
            touchUi: true,
            buttons: [{
                    text: 'Apply',
                    handler: function (event) {
                        var date = calendar.getVal();

                        setInputValue(date[0], date[1] || date[0], calendar.s.dateFormat);
                        popup.close();
                    }
                },
                'cancel'
            ]
        },
        custom: {
            breakpoint: 559,
            buttons: [],
            display: 'anchored',
            anchor: dateInput,
            anchorAlign: 'start',
            touchUi: false,
            scrollLock: false,
            showArrow: false,
            maxWidth: 920
        }
    }
});

function setInputValue(start, end, dateFormat) {
    dateInput.value = formatDate(dateFormat, new Date(start)) + ' - ' + formatDate(dateFormat, new Date(end));
}

dateInput.addEventListener('click', function () {
    popup.open();
});

document.addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('apply-button')) {
        var date = calendar.getVal();

        setInputValue(date[0], date[1] || date[0], calendar.s.dateFormat);
        popup.close();
    } else if (e.target && e.target.classList.contains('cancel-button')) {
        popup.close();
    }
});