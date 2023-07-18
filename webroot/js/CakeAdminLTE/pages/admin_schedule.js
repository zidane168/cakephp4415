var ADMIN_SCHEDULE = {

    registered_dated_2: [], 
    registered_dated_3: [],
    registered_dated_4: [],
    registered_dated_5: [],
    registered_dated_6: [],
    registered_dated_7: [],
    registered_dated_8: [],
 
    dates: [],
    registered_dated: [],
    holidays: [],
 
    init_datepicker: (defaultDate = new Date()) => {
        $('#datepicker-inline').datepicker({   

            firstDay: 0,        // 1: Mon, Tues, .... / 0: Sun, Mon, ... 
            todayHighlight: true,
            defaultDate: defaultDate,
            // minDate: 0,         // disabled past day 
            beforeShowDay: function(date) {  
                var formattedDate = moment(date).format("YYYY-MM-DD");    
                if( $.inArray(formattedDate, ADMIN_SCHEDULE.registered_dated_2) != -1  || $.inArray(formattedDate, ADMIN_SCHEDULE.registered_dated_3) != -1  ||
                    $.inArray(formattedDate, ADMIN_SCHEDULE.registered_dated_4) != -1  || $.inArray(formattedDate, ADMIN_SCHEDULE.registered_dated_5) != -1 ||
                    $.inArray(formattedDate, ADMIN_SCHEDULE.registered_dated_6) != -1  || $.inArray(formattedDate, ADMIN_SCHEDULE.registered_dated_7) != -1 ||
                    $.inArray(formattedDate, ADMIN_SCHEDULE.registered_dated_8) != -1  || 
                    $.inArray(formattedDate, ADMIN_SCHEDULE.registered_dated) != -1 ) {   
                    return [true, "highlight", lang.registered];    // "class=highlight"; tooltip="registered"
                } 

                if( $.inArray(formattedDate, ADMIN_SCHEDULE.holidays) != -1) {   
                    return [true, "highlight_holiday", lang.holidays];   
                } 

                if (date.getDay() == 0) {
                    return [true, "highlight_sunday", ""];   
                }

                return [true];
            } 
        });  
    },

    init_edit_control: () => {
        if ($('#start_date').val() != null) {
            ADMIN_SCHEDULE.refresh_datepicker(new Date($('#start_date').val())) 
        } 

        $('#start_date').on('dp.change', function() {  
            ADMIN_SCHEDULE.refresh_datepicker(new Date($('#start_date').val())) 
        });

        $('#end_date').on('dp.change', function() { 
            ADMIN_SCHEDULE.refresh_datepicker(new Date($('#end_date').val()))  
        });

        $('#number-of-lessons').on('change', function() {
            const weekendsCheckbox = document.querySelectorAll('input[type="checkbox"]');   
            weekendsCheckbox.forEach((checkbox) => {    
                checkbox.checked = false;  
            });
            ADMIN_SCHEDULE.dates = [];
            ADMIN_SCHEDULE.registered_dated = [];
            ADMIN_SCHEDULE.refresh_datepicker(new Date($('#end_date').val()));
        })

        ADMIN_SCHEDULE.arrange_edit_dates()
    },

    init_control: () => {
        $('#start_date').on('dp.change', function() {  
            ADMIN_SCHEDULE.refresh_datepicker(new Date($('#start_date').val())) 
        });

        $('#end_date').on('dp.change', function() { 
            ADMIN_SCHEDULE.refresh_datepicker(new Date($('#end_date').val()))  
        });

        $('#number-of-lessons').on('change', function() {
            const weekendsCheckbox = document.querySelectorAll('input[type="checkbox"]');   
            weekendsCheckbox.forEach((checkbox) => {    
                checkbox.checked = false;  
            });
            ADMIN_SCHEDULE.dates = [];
            ADMIN_SCHEDULE.registered_dated = [];

            ADMIN_SCHEDULE.refresh_datepicker(new Date($('#end_date').val()));
        })

        ADMIN_SCHEDULE.arrange_dates();
    },  

    get_day_within_one_week: (startDate, numberOfLessons,  dateOfWeek) => {
     
        if (startDate == 'Invalid Date') {  
            return; 
        }
        let currentDate = startDate;  

        let day = "";
        do {
            if (currentDate.getDay() ===  dateOfWeek) {    
                day = currentDate;  
                break;
            }
            currentDate.setDate(currentDate.getDate() + 1);  
        } while (1);

        let result = [];
        if (day) {
            result.push(moment(new Date(day)).format('YYYY-MM-DD')); 
            let count = 0;

            do {  
                ++count;
                if (count >= numberOfLessons * 2) {
                    break;
                }
                day.setDate(day.getDate() + 7);   
                if (day.getDay() ===  dateOfWeek) {    
                    result.push(moment(new Date(day)).format('YYYY-MM-DD')); 
                }
    
            } while (1);
        } 
 
        return result; 
    },

    arrange_edit_dates: () => { 
          

        $('.weekend_2').on('change', (event)  => { 

            let numberOfLessons = $('#number-of-lessons').val();   
            let startDate       = new Date($('#start_date').val());  
            if (event.target.checked == true) { 
                ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons, 1));
            } 
            else {
                ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                    let d = new Date(date);
                    return (d.getDay() != 1)
                });
            }  
            ADMIN_SCHEDULE.dates.sort();  
            ADMIN_SCHEDULE.check_holiday();  
        }); 

        $('.weekend_3').on('change', (event)  => { 

            let numberOfLessons = $('#number-of-lessons').val();   
            let startDate       = new Date($('#start_date').val()); 
            if (event.target.checked == true) { 
                ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons, 2));
    
            } 
            else {
                ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                    let d = new Date(date);
                    return (d.getDay() != 2)
                });
            }  
            ADMIN_SCHEDULE.dates.sort();  
            ADMIN_SCHEDULE.check_holiday();  
        }); 

        $('.weekend_4').on('change', (event)  => { 

            let numberOfLessons = $('#number-of-lessons').val();   
            let startDate       = new Date($('#start_date').val());  
            if (event.target.checked == true) { 
                ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons, 3));
            } 
            else {
                ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                    let d = new Date(date);
                    return (d.getDay() != 3)
                });
            }  
            ADMIN_SCHEDULE.dates.sort();  
            ADMIN_SCHEDULE.check_holiday();  
        }); 

        $('.weekend_5').on('change', (event)  => { 

            let numberOfLessons = $('#number-of-lessons').val();   
            let startDate       = new Date($('#start_date').val());  
            if (event.target.checked == true) { 
                ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons, 4));
             } 
            else {
                ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                    let d = new Date(date);
                    return (d.getDay() != 4)
                });
            }  
            ADMIN_SCHEDULE.dates.sort();  
            ADMIN_SCHEDULE.check_holiday();  
        }); 

        $('.weekend_6').on('change', (event)  => { 

            let numberOfLessons = $('#number-of-lessons').val();   
            let startDate       = new Date($('#start_date').val());  
            if (event.target.checked == true) { 
                ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons, 5));
             } 
            else {
                ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                    let d = new Date(date);
                    return (d.getDay() != 5)
                });
            }  
            ADMIN_SCHEDULE.dates.sort();  
            ADMIN_SCHEDULE.check_holiday();  
        }); 

        $('.weekend_7').on('change', (event)  => { 

            let numberOfLessons = $('#number-of-lessons').val();   
            let startDate       = new Date($('#start_date').val());  
            if (event.target.checked == true) { 
                ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons, 6));
             } 
            else {
                ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                    let d = new Date(date);
                    return (d.getDay() != 6)
                });
            }  
            ADMIN_SCHEDULE.dates.sort();  
            ADMIN_SCHEDULE.check_holiday();  
        }); 

        $('.weekend_8').on('change', (event)  => { 

            let numberOfLessons = $('#number-of-lessons').val();   
            let startDate       = new Date($('#start_date').val());  
            if (event.target.checked == true) { 
                ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons, 0));
             } 
            else {
                ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                    let d = new Date(date);
                    return (d.getDay() != 0)
                });
            }  
            ADMIN_SCHEDULE.dates.sort();  
            ADMIN_SCHEDULE.check_holiday();  
        }); 
    },

    arrange_dates: () => { 
      
        const weekendsCheckbox = document.querySelectorAll('input[type="checkbox"]');   
  
        weekendsCheckbox.forEach((checkbox) => {     
            checkbox.addEventListener('change', (event) => { 
                let numberOfLessons = $('#number-of-lessons').val();   
                let startDate       = new Date($('#start_date').val()); 
                
                if (event.target.checked)  {     
                   
                    if (checkbox.classList.value === 'weekend_2') { // every Mon check
                        ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons, 1));

                    }  if (checkbox.classList.value === 'weekend_3') {  
                        ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons,  2));

                    }  if (checkbox.classList.value === 'weekend_4') {  
                        ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons,  3));

                    }  if (checkbox.classList.value === 'weekend_5') {  
                        ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons,  4));
                        
                    }  if (checkbox.classList.value === 'weekend_6') { 
                        ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons,  5));

                    }  if (checkbox.classList.value === 'weekend_7') {  
                        ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons,  6));

                    }  if (checkbox.classList.value === 'weekend_8') {  
                        ADMIN_SCHEDULE.dates.push(...ADMIN_SCHEDULE.get_day_within_one_week(startDate, numberOfLessons,  0));
                    }

                } else {  
 
                    if (checkbox.classList.value === 'weekend_2') { // every Mon check
                        ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                            let d = new Date(date);
                            return (d.getDay() != 1)
                        });
                    }
                    if (checkbox.classList.value === 'weekend_3') {
                        ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                            let d = new Date(date);
                            return (d.getDay() != 2)
                        })
                    }
                    if (checkbox.classList.value === 'weekend_4') {
                        ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                            let d = new Date(date);
                            return (d.getDay() != 3)
                        })
                    }
                    if (checkbox.classList.value === 'weekend_5') {
                        ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter((date) => {
                            let d = new Date(date);
                            return (d.getDay() != 4);
                        })
                    }
                    if (checkbox.classList.value === 'weekend_6') {
                        ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                            let d = new Date(date);
                            return (d.getDay() != 5);
                        })
                    } 
                    if (checkbox.classList.value === 'weekend_7') {
                        ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                            let d = new Date(date);
                            return (d.getDay() != 6);
                        })
                    }
                    if (checkbox.classList.value === 'weekend_8') {
                        ADMIN_SCHEDULE.dates = ADMIN_SCHEDULE.dates.filter(date => {
                            let d = new Date(date);
                            return (d.getDay() != 0);
                        })
                    }
                }   
                 
                ADMIN_SCHEDULE.dates.sort();  
                ADMIN_SCHEDULE.check_holiday();  
            })
        });    
    },

    check_holiday: () => {
        // check holidays
        let count = 0; 
        let isExistHoliday = false;
        let numberOfLessons = $('#number-of-lessons').val();  
        let endDate = $('#end_date').val(); 

        ADMIN_SCHEDULE.registered_dated = []; 
        for (const date of ADMIN_SCHEDULE.dates) {  
            let is_same = false; 

            if ($.inArray(date, ADMIN_SCHEDULE.holidays) != -1) {  // found  
                is_same = true;
            }

            if (is_same === true) { 
                isExistHoliday = true;
                continue; 
            }

            ++count;
            ADMIN_SCHEDULE.registered_dated.push(date)  
            if (count >= numberOfLessons) { 
                break; 
            }
        } 

        const length = ADMIN_SCHEDULE.registered_dated.length;

        if (length) {
            if (isExistHoliday || (endDate != 'Invalid Date' && ADMIN_SCHEDULE.registered_dated[length - 1] != endDate) ) { 
                $('#end_date').val(ADMIN_SCHEDULE.registered_dated[length - 1])
                // bootbox.confirm({
                //     message: lang.is_exits_holiday_change_date_end_info,
                //     buttons: {
                //         confirm: {
                //             label: 'Yes',
                //             className: 'btn-success'
                //         }, 
                //     },
                //     callback: function (result) { 
                //         $('#end_date').val(ADMIN_SCHEDULE.registered_dated[length - 1])
                //     }        
                // }); 
            } 
        } else {
            let start_date = new Date($('#start_date').val());
            let end_date = new Date($('#start_date').val());
            end_date.setDate(start_date.getDate() + 30);   
            let format_endDate = moment(new Date(end_date)).format('YYYY-MM-DD');
            $('#end_date').val(format_endDate);
        }
         
        ADMIN_SCHEDULE.refresh_datepicker();

    },

    refresh_datepicker: (date = $('#end_date').val()) => {

        // refresh datepicker 
        $("#datepicker-inline").datepicker("destroy");      
        ADMIN_SCHEDULE.init_datepicker(new Date(date));
    },
 
}