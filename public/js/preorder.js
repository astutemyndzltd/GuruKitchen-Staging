let preorderContainer = document.querySelector('div.preorder-container');
let divWeekdays = Array.prototype.slice.call(preorderContainer.children);
let btnSaveRestaurant = document.querySelector('button#save-restaurant');
let hdnOpeningTimes = document.querySelector('#opening_times');

let state = {};

for (let divWeekday of divWeekdays) {

    let cbxDay = divWeekday.querySelector('input[type=checkbox]');
    let lblDay = divWeekday.querySelector('label.control-label');
    let divTimings = divWeekday.querySelector('div.timings');
    let aAddHours = divWeekday.querySelector('a.add-hrs');

    let day = lblDay.getAttribute('for');

    state[day] = cbxDay.checked ? [] : null;

    for (let i = 0; i < divTimings.children.length; i++) {

        if (divTimings.children[0].constructor.name !== 'HTMLSpanElement') {

            let divTiming = divTimings.children[i];
            let txtStartTime = divTiming.children[0];
            let txtEndTime = divTiming.children[1];
            let btnRemoveTiming = divTiming.children[2];

            let slot = { opens_at: txtStartTime.value, closes_at: txtEndTime.value };
            state[day].push(slot);

            let $txtStartTimePicker = $(txtStartTime).mdtimepicker();
            let $txtEndTimePicker = $(txtEndTime).mdtimepicker();

            $txtStartTimePicker.on('timechanged', e => slot.opens_at = e.value);
            $txtEndTimePicker.on('timechanged', e => slot.closes_at = e.value);

            btnRemoveTiming.onclick = () => {
                $(divTiming).remove();
                state[day].splice(state[day].indexOf(slot), 1);

                if (state[day].length == 0) {
                    $(divTimings).trigger('childless');
                }
            };
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

    $(aAddHours).on('click', () => {
        if (!cbxDay.checked) return;
        if (state[day] == null) state[day] = [];
        addHours(divTimings, state[day]);
    });

}

function addHours(divTimings, slots) {

    let slot = { opens_at: '10:00 AM', closes_at: '10:00 PM' };
    slots.push(slot);

    let $divTiming = $('<div class="timing"></div>');
    let $txtStartTime = $(`<input type="text" readonly class="start" placeholder="Start time" value="10:00 AM">`);
    let $txtEndTime = $(`<input type="text" readonly class="end" placeholder="End Time" value="10:00 PM" >`);
    let $btnRemove = $(`<button type="button"><i class="fa fa-trash" aria-hidden="true"></i></button>`);

    $divTiming.appendTo(divTimings);
    $divTiming.append($txtStartTime).append($txtEndTime);
    $divTiming.append($btnRemove);

    let $txtStartTimePicker = $txtStartTime.mdtimepicker();
    let $txtEndTimePicker = $txtEndTime.mdtimepicker();

    $txtStartTimePicker.on('timechanged', e => slot.opens_at = e.value);
    $txtEndTimePicker.on('timechanged', e => slot.closes_at = e.value);

    $btnRemove[0].onclick = () => {
        slots.splice(slots.indexOf(slot), 1);
        $divTiming.remove();

        if (slots.length == 0) {
            $(divTimings).trigger('childless');
        }
    };

}

$(btnSaveRestaurant).on('click', () => $(hdnOpeningTimes).val(JSON.stringify(state)));

