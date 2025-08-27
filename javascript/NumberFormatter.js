class NumberFormatter {
  constructor() {
    if (NumberFormatter.instance) {
      return NumberFormatter.instance; // ถ้ามี instance อยู่แล้วให้รีเทิร์นเลย
    }

    this.locale = 'en-US';
    this.currency = null;

    NumberFormatter.instance = this;
  }

  // ตั้งค่า locale และ currency
  setConfig(locale = 'en-US', currency = null) {
    this.locale = locale;
    this.currency = currency;
  }

  // ฟอร์แมตตัวเลข -> #,###.00
  format(num) {
    return new Intl.NumberFormat(this.locale, {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(num);
  }

  // ฟอร์แมตตัวเลข -> เงิน
  formatCurrency(num) {
    if (!this.currency) {
      throw new Error("Currency is not set in NumberFormatter.");
    }
    return new Intl.NumberFormat(this.locale, {
      style: 'currency',
      currency: this.currency,
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(num);
  }
}

// ✅ สร้าง instance เดียว (Singleton)
const numberFormatter = new NumberFormatter();
Object.freeze(numberFormatter); // กันไม่ให้แก้ไข instance

export default numberFormatter;
