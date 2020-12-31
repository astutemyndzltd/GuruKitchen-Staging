let preorderContainer = document.querySelector('div.preorder-container');
let divWeekdays = Array.prototype.slice.call(preorderContainer.children);

let state = {};

for (let divWeekday of divWeekdays) {

    let cbxDay = divWeekday.querySelector('input[type=checkbox]');
    let lblDay = divWeekday.querySelector('label.control-label');
    let divTimings = divWeekday.querySelector('div.timings');
    let aAddHours = divWeekday.querySelector('a.add-hrs');

    console.log(lblDay);

    state[lblDay.for] = cbxDay.checked ? [] : null;

    for (let i = 0; i < divTimings.children.length; i++) {

        if (divTimings.children[0].constructor.name === 'HTMLSpanElement') {
            let span = divTimings.children[0];
        }
        else {
            let divTiming = divTimings.children[0];
            let txtStartTime = divTiming.children[0];
            let txtEndTime = divTiming.children[1];
            let btnRemoveTiming = divTiming.children[2];

            state[lblDay.for].push({ opens_at: txtStartTime.value, closes_at: txtEndTime.value });
        }

    }

}

console.log(state);