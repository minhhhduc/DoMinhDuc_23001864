const { createApp } = Vue;

const app = createApp({
    data() {
        return {
            cartItems: [
                { id: 1, name: 'Bút bi', unitPrice: 5000, quantity: 2 },
                { id: 2, name: 'Quyển vở', unitPrice: 12000, quantity: 3 }
            ]
        };
    },
    computed: {
        totalQuantity() {
            return this.cartItems.reduce((total, item) => total + item.quantity, 0);
        },
        totalAmount() {
            return this.cartItems.reduce(
                (total, item) => total + item.unitPrice * item.quantity,
                0
            );
        }
    },
    methods: {
        formatPrice(value) {
            return formatCurrency(value);
        },
        handleQuantityChange(id, newQuantity) {
            
            if (
                !Number.isInteger(newQuantity) ||
                newQuantity < CART_QUANTITY_MIN ||
                newQuantity > CART_QUANTITY_MAX
            ) {
                return;
            }

            const item = this.cartItems.find((cartItem) => cartItem.id === id);

            if (!item) {
                return;
            }

            this.cartItems = this.cartItems.map((cartItem) => (
                cartItem.id === id ? { ...cartItem, quantity: newQuantity } : cartItem
            ));
        },
        handleRemove(id) {
            if (!this.cartItems.some((item) => item.id === id)) {
                return;
            }

            this.cartItems = this.cartItems.filter((item) => item.id !== id);
        }
    }
});

app.component('dong-gio-hang', window.CartItem);
app.mount('#app');
