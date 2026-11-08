const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0
});

function formatCurrency(value) {
    return currencyFormatter.format(value);
}
