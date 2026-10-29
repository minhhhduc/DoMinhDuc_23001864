const { createApp } = Vue;

createApp({
    data() {
        return {
            customerName: '',
            showtime: '19:00',
            ticketType: 'standard',
            quantity: 2,
            popcornCombo: false,
            confirmation: null,
            ticketPrices: {
                standard: 70000,
                vip: 100000
            },
            comboPrice: 45000,
            showtimes: [
                { value: '09:30', label: '09:30 - Phòng 1' },
                { value: '16:45', label: '16:45 - Phòng 2' },
                { value: '19:00', label: '19:00 - Phòng 1' },
                { value: '21:15', label: '21:15 - Phòng 3' }
            ]
        };
    },
    computed: {
        isQuantityValid() {
            const quantity = Number(this.quantity);
            return Number.isInteger(quantity) && quantity >= 1 && quantity <= 10;
        },
        quantityError() {
            if (this.quantity === '' || this.quantity === null) {
                return 'Vui lòng nhập số lượng vé.';
            }
            const quantity = Number(this.quantity);
            if (!Number.isFinite(quantity)) {
                return 'Số lượng phải là một con số.';
            }
            if (!Number.isInteger(quantity)) {
                return 'Số lượng phải là số nguyên.';
            }
            if (quantity < 1 || quantity > 10) {
                return 'Số lượng phải từ 1 đến 10 vé.';
            }
            return '';
        },
        unitPrice() {
            return this.ticketPrices[this.ticketType];
        },
        comboFee() {
            return this.popcornCombo ? this.comboPrice : 0;
        },
        totalPrice() {
            if (!this.isQuantityValid) {
                return 0;
            }
            return this.unitPrice * Number(this.quantity) + this.comboFee;
        },
        showtimeLabel() {
            const showtime = this.showtimes.find(item => item.value === this.showtime);
            return showtime ? showtime.label : '—';
        },
        ticketTypeLabel() {
            return this.ticketType === 'vip' ? 'Vé VIP' : 'Vé thường';
        },
        canSubmit() {
            return this.customerName.trim() !== '' && this.isQuantityValid;
        }
    },
    methods: {
        formatCurrency(amount) {
            return Number(amount).toLocaleString('vi-VN') + ' đ';
        },
        increaseQuantity() {
            const quantity = Number(this.quantity) || 0;
            this.quantity = Math.min(10, Math.floor(quantity) + 1);
        },
        decreaseQuantity() {
            const quantity = Number(this.quantity) || 2;
            this.quantity = Math.max(1, Math.ceil(quantity) - 1);
        },
        submitBooking() {
            if (!this.canSubmit) {
                return;
            }
            this.confirmation = {
                customerName: this.customerName,
                showtimeLabel: this.showtimeLabel,
                ticketTypeLabel: this.ticketTypeLabel,
                quantity: Number(this.quantity),
                hasCombo: this.popcornCombo,
                totalPrice: this.totalPrice
            };
        },
        resetForm() {
            this.customerName = '';
            this.showtime = '19:00';
            this.ticketType = 'standard';
            this.quantity = 2;
            this.popcornCombo = false;
            this.confirmation = null;
        }
    }
}).mount('#app');
