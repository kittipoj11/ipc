// app.js

// รอให้หน้าเว็บโหลดเสร็จก่อนเริ่มทำงาน
document.addEventListener('DOMContentLoaded', () => {
    const orderForm = document.getElementById('order-form');
    const orderListBody = document.getElementById('order-list-body');

    const API_URL = 'api_order_handler.php';

    // --- 1. ฟังก์ชันสำหรับดึงและแสดงผลออเดอร์ทั้งหมด ---
    const fetchAndRenderOrders = async () => {
        try {
            const response = await fetch(API_URL); // GET request by default
            const result = await response.json();

            orderListBody.innerHTML = ''; // ล้างข้อมูลเก่า

            if (result.status === 'success' && result.data.length > 0) {
                result.data.forEach(order => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${order.order_id}</td>
                        <td>${order.customer_name}</td>
                        <td>${new Date(order.order_date).toLocaleDateString('th-TH')}</td>
                        <td>${parseFloat(order.grand_total).toFixed(2)}</td>
                        <td><span class="delete-btn" data-id="${order.order_id}">ลบ</span></td>
                    `;
                    orderListBody.appendChild(row);
                });
            } else {
                orderListBody.innerHTML = '<tr><td colspan="5">ไม่พบข้อมูลออเดอร์</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    };

    // --- 2. จัดการการ submit ฟอร์มเพื่อสร้างออเดอร์ ---
    orderForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // ป้องกันไม่ให้ฟอร์มโหลดหน้าใหม่

        // รวบรวมข้อมูลจากฟอร์มเพื่อสร้าง object ที่ตรงกับ PHP
        const orderData = {
            customer_name: document.getElementById('customer-name').value,
            order_date: new Date().toISOString().slice(0, 19).replace('T', ' '), // วันที่ปัจจุบัน
            grand_total: document.getElementById('grand-total').value,
            details: [{
                item_name: document.querySelector('.item-name').value,
                quantity: document.querySelector('.item-quantity').value,
                price: document.querySelector('.item-price').value
            }],
            periods: [] // ในตัวอย่างนี้ยังไม่มีการกรอกงวดชำระ
        };

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'create',
                    data: orderData
                })
            });

            const result = await response.json();
            alert(result.message);

            if (result.status === 'success') {
                orderForm.reset(); // ล้างฟอร์ม
                fetchAndRenderOrders(); // โหลดรายการออเดอร์ใหม่
            }
        } catch (error) {
            console.error('Error creating order:', error);
        }
    });

    // --- 3. จัดการการคลิกปุ่มลบ (Event Delegation) ---
    orderListBody.addEventListener('click', async (event) => {
        if (event.target.classList.contains('delete-btn')) {
            const orderId = event.target.dataset.id;
            
            if (confirm(`คุณต้องการลบออเดอร์ ID: ${orderId} ใช่หรือไม่?`)) {
                try {
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: 'delete',
                            order_id: orderId
                        })
                    });
                    const result = await response.json();
                    alert(result.message);
                    if (result.status === 'success') {
                        fetchAndRenderOrders(); // โหลดรายการออเดอร์ใหม่
                    }
                } catch (error) {
                    console.error('Error deleting order:', error);
                }
            }
        }
    });

    // --- เรียกใช้ฟังก์ชันเพื่อแสดงผลครั้งแรกเมื่อเปิดหน้า ---
    fetchAndRenderOrders();
});