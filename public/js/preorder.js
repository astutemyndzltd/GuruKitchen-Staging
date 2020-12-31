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

            let startTime = txtStartTime.value;
            let endTime = txtEndTime.value;

            //$(txtStartTime).mdtimepicker();
            //$(txtStartTime).mdtimepicker('setValue', startTime);
            //$(txtEndTime).mdtimepicker();
            //$(txtEndTime).mdtimepicker('setValue', startTime);

            state[day].push({ opens_at: startTime, closes_at: endTime });
        }

    }

}

console.log(state);