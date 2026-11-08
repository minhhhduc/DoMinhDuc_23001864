const { createApp } = Vue;

const app = createApp({
    data() {
        return {
            purchaseMessage: '',
            products: [
                {
                    id: 1,
                    name: 'iPhone 15 Pro Max',
                    price: 34990000,
                    image: 'https://thumb.wikimedia.org/wikipedia/commons/thumb/1/19/Apple_iPhone_15_Pro.jpg/960px-Apple_iPhone_15_Pro.jpg',
                    description: 'Khung titan, chip A17 Pro, camera 48MP và màn hình ProMotion 6,7 inch.',
                    stock: 6
                },
                {
                    id: 2,
                    name: 'Samsung Galaxy S24 Ultra',
                    price: 29990000,
                    image: 'https://thumb.wikimedia.org/wikipedia/commons/thumb/1/1e/Samsung_Galaxy_S24_Ultra_with_retail_box%2C_rear.jpg/960px-Samsung_Galaxy_S24_Ultra_with_retail_box%2C_rear.jpg',
                    description: 'Màn hình phẳng 6,8 inch, bút S Pen, camera 200MP và các tính năng Galaxy AI.',
                    stock: 0
                },
                {
                    id: 3,
                    name: 'MacBook Air M3',
                    price: 27990000,
                    image: 'https://thumb.wikimedia.org/wikipedia/commons/thumb/2/25/M2_Macbook_Air_Midnight_model_-_1.jpg/960px-M2_Macbook_Air_Midnight_model_-_1.jpg',
                    description: 'Chip Apple M3, màn hình Liquid Retina 13,6 inch, thời lượng pin lên đến 18 giờ.',
                    stock: 4
                },
                {
                    id: 4,
                    name: 'Tai nghe Sony WH-1000XM5',
                    price: 7490000,
                    image: 'https://i.rtings.com/assets/products/Nc33W9lA/sony-wh-1000xm5-wireless/design-medium.jpg?format=auto',
                    description: 'Chống ồn chủ động, âm thanh Hi-Res và thời lượng pin lên đến 30 giờ.',
                    stock: 0
                },
                {
                    id: 5,
                    name: 'Bàn phím cơ Keychron K8',
                    price: 1890000,
                    image: 'https://thumb.wikimedia.org/wikipedia/commons/thumb/f/fb/Keychron_K8_Non-Backlight_Wireless_Mechanical_Keyboard.jpg/960px-Keychron_K8_Non-Backlight_Wireless_Mechanical_Keyboard.jpg',
                    description: 'Bàn phím cơ không dây, kết nối Bluetooth hoặc USB-C, hỗ trợ Windows và macOS.',
                    stock: 12
                },
                {
                    id: 6,
                    name: 'Chuột Logitech MX Master 3S',
                    price: 2490000,
                    image: 'https://thumb.wikimedia.org/wikipedia/commons/thumb/d/d4/Logitech_MX_Master_3S_HS12.jpg/960px-Logitech_MX_Master_3S_HS12.jpg',
                    stock: 9
                }
            ]
        };
    },
    computed: {
        inStockCount() {
            return this.products.filter((product) => product.stock > 0).length;
        }
    },
    methods: {
        handleBuy(name) {
            this.purchaseMessage = `Đã mua "${name}" thành công.`;
        }
    }
});

app.component('the-san-pham', window.ProductCard);
app.mount('#app');
