const txtPayoutPeriod = document.querySelector('#txtPayoutPeriod');
const ddlRestaurants = document.querySelector('#restaurant_id');
const txtAmount = document.querySelector('#amount');

let datePickerOptions = {

    locale: { 
        format: 'DD MMM YYYY', 
        cancelLabel: 'Clear' 
    },

    autoUpdateInput: false 
};


function onDateRangeChange(start, end) {

}


const daterangepicker = $(txtPayoutPeriod).daterangepicker(datePickerOptions, onDateRangeChange);

$(txtPayoutPeriod).on('apply.daterangepicker', (ev, picker) => {
    $(this).val(picker.startDate.format('DD MMM YYYY') + ' - ' + picker.endDate.format('DD MMM YYYY'));
});

$(txtPayoutPeriod).on('cancel.daterangepicker', (ev, picker) => $(this).val(''));