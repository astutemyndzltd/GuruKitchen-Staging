let preorderContainer = document.querySelector('div.preorder-container');
let divWeekdays = Array.prototype.slice.call(preorderContainer.children);

let state = {};

for (let divWeekday of divWeekdays) {

    let cbxDay = divWeekday.querySelector('input[type=checkbox]');
    let lblDay = divWeekday.querySelector('label.control-label');
    let divTimings = divWeekday.querySelector('div.timings');
    let aAddHours = divWeekday.querySelector('a.add-hrs');

    let day = lblDay.getAttribute('for');

    state[day] = cbxDay.checked ? [] : null;

    for (let i = 0; i < divTimings.children.length; i++) {

        if (divTimings.children[0].constructor.name === 'HTMLSpanElement') {
            let span = divTimings.children[0];
        }
        else {
            let divTiming = divTimings.children[i];
            let txtStartTime = divTiming.children[0];
            let txtEndTime = divTiming.children[1];
            let btnRemoveTiming = divTiming.children[2];

            $(txtStartTime).mdtimepicker();
            $(txtEndTime).mdtimepicker();

            btnRemoveTiming.onclick = () => {
                $(divTiming).remove();
                state[day].splice(i, 1);

                if (state[day].length == 0) {
                    $(divTimings).trigger('childless');
                }
            };

            state[day].push({ opens_at: txtStartTime.value, closes_at: txtEndTime.value });
        }

    }

    $(cbxDay).on('ifChecked', () => {
        state[day] = [];
        divTimings.innerHTML = '';
        addHours(divTimings, state[day]);
    });

    $(cbxDay).on('ifUnchecked', () => {
        state[day] = null;
        divTimings.innerHTML = `<span>Closed all day</span>`;
    });

    $(divTimings).on('childless', () => {
        state[day] = null;
        divTimings.innerHTML = `<span>Closed all day</span>`;
        $(cbxDay).iCheck('uncheck');
    });

}

function addHours(divTimings, slots) {

    let index = slots.push({ opens_at: '10:00 AM', closes_at: '10:00 PM' });

    let $divTiming = $('<div class="timing"></div>');
    let $txtStartTime = $(`<input type="text" readonly class="start" placeholder="Start time" value="10:00 AM">`);
    let $txtEndTime = $(`<input type="text" readonly class="end" placeholder="End Time" value="10:00 PM" >`);
    let $btnRemove = $(`<button type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>`);

    $divTiming.appendTo(divTimings);
    $divTiming.append($txtStartTime).append($txtEndTime);
    $divTiming.append($btnRemove);

    $txtStartTime.mdtimepicker();
    $txtEndTime.mdtimepicker();

    $btnRemove[0].onclick = function () {

        slots.splice(index, 1);
        $divTiming.remove();

        if (slots.length == 0) {
            $(divTimings).trigger('childless');
        }

    };

}