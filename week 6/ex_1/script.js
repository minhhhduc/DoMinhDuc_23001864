const { createApp } = Vue;

createApp({
    data() {
        return {
            studentId: 'SV2024001',
            fullName: 'Nguyễn Văn An',
            studentClass: 'CNTT K20A',
            avatarUrl: "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'><rect width='200' height='200' fill='%23dbeafe'/><circle cx='100' cy='76' r='36' fill='%232563eb'/><path d='M28 200c0-42 32-72 72-72s72 30 72 72z' fill='%232563eb'/></svg>",
            gpa: 8.2,
            isStudying: true,
            showIntro: true
        };
    },
    computed: {
        formattedGpa() {
            const gpa = Number(this.gpa);
            if (!Number.isFinite(gpa)) {
                return '—';
            }
            return gpa.toLocaleString('vi-VN', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            });
        },
        rankCode() {
            const gpa = Number(this.gpa);
            if (gpa >= 8) return 'excellent';
            if (gpa >= 6.5) return 'good';
            if (gpa >= 5) return 'average';
            return 'fail';
        },
        rankLabel() {
            return {
                excellent: 'Giỏi',
                good: 'Khá',
                average: 'Trung bình',
                fail: 'Chưa đạt'
            }[this.rankCode];
        },
        rankClass() {
            return 'badge--' + this.rankCode;
        }
    }
}).mount('#app');
