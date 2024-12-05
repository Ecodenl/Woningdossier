export default (name, enableTime = false) => ({
    showDatepicker: false,
    datepickerValue: '',
    year: 2000,
    month: 0,
    hours: 0,
    minutes: 0,
    no_of_days: [],
    blankDays: [],
    name: name,
    mode: 'day',
    enableTime: !! enableTime,

    init() {
        // Set custom DOM prop so we can access methods and values here from the DOM.
        this.$el.datepicker = this;

        const date = this.$refs[this.name].value;
        const d = date ? new Date(date) : new Date();

        this.year = d.getFullYear();
        this.month = d.getMonth();
        this.hours = d.getHours();
        this.minutes = d.getMinutes();

        if (date) {
            this.setDateValue(d.getDate());
        }

        this.getNoOfDays();

        if (this.enableTime) {
            this.$watch('hours', (value, oldValue) => {
                if (value === null || value < 0 || value > 23) {
                    // This will cause the $watch to trigger once more, but will reset any non-numeric/invalid values
                    // to something sensible.
                    this.hours = 0;
                } else {
                    this.setDateValue(this.getCurrentDay());
                }
            });
            this.$watch('minutes', (value, oldValue) => {
                if (value === null || value < 0 || value > 59) {
                    // This will cause the $watch to trigger once more, but will reset any non-numeric/invalid values
                    // to something sensible.
                    this.minutes = 0;
                } else {
                    this.setDateValue(this.getCurrentDay());
                }
            });
        }

        this.$watch('showDatepicker', (value, oldValue) => {
            if (value === false) {
                // Should be the x-data .datepicker already, but just in case.
                this.$el.closest('.datepicker').triggerCustomEvent('datepicker-closed');
            }
        });
    },
    isToday(day) {
        const today = new Date();
        const d = new Date(this.year, this.month, day);
        return this.formatDate(today) === this.formatDate(d);
    },
    isSelected(day) {
        const d = new Date(this.year, this.month, day, this.hours, this.minutes);
        return this.datepickerValue === this.formatDate(d, this.enableTime);
    },
    setDateValue(day) {
        let selectedDate = new Date(this.year, this.month, day, this.hours, this.minutes);
        this.datepickerValue = this.formatDate(selectedDate, this.enableTime);
        // Format date in YYYY-MM-DD format. We slice -2 to remove the leading zero if amount of chars is more than 2
        // (e.g. when on month 10, it will format to 011, and then sliced to 11).
        let dateValue = `${selectedDate.getFullYear()}-${('0' + (selectedDate.getMonth() + 1)).slice(-2)}-${('0' + selectedDate.getDate()).slice(-2)}`;

        if (enableTime) {
            // Same slicing logic as above
            dateValue += ` ${('0' + this.hours).slice(-2)}:${('0' + this.minutes).slice(-2)}`
        }

        let currentValue = this.$refs[this.name].value;
        if (currentValue !== dateValue) {
            // Only change if actually changed
            this.$refs[this.name].value = dateValue;
            window.triggerEvent(this.$refs[this.name], 'change');
        }

        if (this.enableTime && this.$el?.classList?.contains('day-selector')) {
            this.$el.closest('.datepicker').querySelector('.hours').focus();
        } else if (! this.enableTime) {
            this.showDatepicker = false;
        }
    },
    getNoOfDays() {
        let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
        // find where to start calendar day of week
        let dayOfWeek = new Date(this.year, this.month).getDay();
        let blankDaysArray = [];
        for (let i = 1; i <= dayOfWeek; i++) {
            blankDaysArray.push(i);
        }
        let daysArray = [];
        for (let j = 1; j <= daysInMonth; j++) {
            daysArray.push(j);
        }
        this.blankDays = blankDaysArray;
        this.no_of_days = daysArray;
    },
    // Formatting
    formatDate(date, withTime = false) {
        return withTime
            ? date.toLocaleString('nl', {
                weekday: "short",
                year: "numeric",
                month: "numeric",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            })
            : date.toLocaleDateString('nl', {weekday: "short", year: "numeric", month: "numeric", day: "numeric"});
    },
    // Browsing related methods
    leftClick()
    {
        if (this.mode === 'day') {
            this.subMonth();
        } else {
            this.subYear();
        }
    },
    rightClick()
    {
        if (this.mode === 'day') {
            this.addMonth();
        } else {
            this.addYear();
        }
    },
    setMonth(month) {
        this.month = month;
        this.mode = 'day';
        this.getNoOfDays();
    },
    subMonth() {
        if (this.month === 0) {
            this.month = 11;
            this.year -= 1;
        } else {
            this.month -= 1;
        }
        this.getNoOfDays();
    },
    addMonth() {
        if (this.month === 11) {
            this.month = 0;
            this.year += 1;
        } else {
            this.month += 1;
        }
        this.getNoOfDays();
    },
    subYear() {
        this.year -= 1;
        this.getNoOfDays();
    },
    addYear() {
        this.year += 1;
        this.getNoOfDays();
    },
    getCurrentDay() {
        const current = this.$refs[this.name].value;
        if (current) {
            return (new Date(current)).getDate();
        }
    },
    setDate(date) {
        if (date.length > 0) {
            date = new Date(date);
            this.year = date.getFullYear();
            this.month = date.getMonth();
            this.hours = date.getHours();
            this.minutes = date.getMinutes();
            this.setDateValue(date.getDate());
        } else {
            this.datepickerValue = '';
            this.$refs[this.name].value = '';
        }
    }
});
