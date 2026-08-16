# Quick Reference - Admin Features

## 🎯 Access Admin Interface

**URL:** `http://localhost:8000/admin`

**Login as:** Admin account

---

## 📋 Customer Management

### List & Search
- **URL:** `/admin/customers`
- **Search:** Name, Email, Phone
- **Filter:** Role, Date Range
- **Export:** CSV download available

### View Customer
- **URL:** `/admin/customers/{id}`
- **Shows:**
  - Profile information
  - Total orders & spending
  - Order status breakdown
  - Recent orders list

### Edit Customer
- **URL:** `/admin/customers/{id}/edit`
- **Update:** Name, Email, Phone, Address, DOB, Gender

### Delete Customer
- **Action:** From list or details page
- **Deletes:** Customer and all associated data

---

## 🏪 Seller Management

### List & Search
- **URL:** `/admin/sellers`
- **Search:** Name, Email, Phone
- **Filter:** Date Range
- **Status:** Pending/Approved badge
- **Export:** CSV download available

### View Seller
- **URL:** `/admin/sellers/{id}`
- **Shows:**
  - Profile information
  - Approval status
  - Product count
  - Sales statistics
  - Recent products

### Approve Seller
- **Action:** From seller details page
- **Effect:** Sets `email_verified_at` to current time
- **Result:** Seller becomes active

### Reject Seller
- **Action:** From seller details page
- **Effect:** Clears `email_verified_at`
- **Result:** Seller becomes inactive

### Edit Seller
- **URL:** `/admin/sellers/{id}/edit`
- **Update:** Name, Email, Phone, Address

### Delete Seller
- **Action:** From list or details page
- **Warning:** ⚠️ Deletes seller AND all their products
- **Confirm:** Confirmation dialog required

---

## 📊 Dashboard Stats

### Customer Stats
| Stat | Shows |
|------|-------|
| Total Orders | Count of all orders |
| Total Spent | Sum of order totals |
| To Pay | Orders pending payment |
| Confirmed | Orders confirmed |
| Shipping | Orders in transit |
| Delivered | Completed orders |
| Cancelled | Cancelled orders |

### Seller Stats
| Stat | Shows |
|------|-------|
| Total Products | All products by seller |
| Active Products | Visible/active products |
| Total Orders | Orders received |
| Total Sales | Revenue from orders |
| Products Display | Grid of recent items |

---

## 🔍 Search & Filter Examples

### Find Customers
```
Search: "john"         → Shows all customers named John
Search: "gmail"        → Shows customers with Gmail
Filter: Customer role  → Shows only customers (not sellers)
Date: Jan 1 - Jan 31  → Shows customers registered in January
```

### Find Sellers
```
Search: "store"        → Shows sellers with Store in name
Search: "seller@test"  → Shows seller with that email
Filter: Last 30 days   → Shows recently registered sellers
Status: Pending        → Shows unapproved sellers only
```

---

## 📥 Export Data

### Customer Export
- **File:** `customers_2026-01-29_14-30-00.csv`
- **Columns:** ID, Name, Email, Phone, Role, Address, Date
- **Use:** Excel, Google Sheets, analysis tools

### Seller Export
- **File:** `sellers_2026-01-29_14-30-00.csv`
- **Columns:** ID, Name, Email, Phone, Products, Status, Date
- **Use:** Excel, Google Sheets, reporting

---

## 🔐 Permissions

✅ **Admin Only** - All routes require:
- Authentication (`auth`)
- Admin gate (`can:access-admin`)

❌ **Cannot Access**
- Regular customers
- Sellers
- Guests

---

## ⚡ Quick Links

| Task | URL |
|------|-----|
| All Customers | `/admin/customers` |
| Edit Customer | `/admin/customers/2/edit` |
| Customer Details | `/admin/customers/2` |
| All Sellers | `/admin/sellers` |
| Edit Seller | `/admin/sellers/1/edit` |
| Seller Details | `/admin/sellers/1` |
| Export Customers | `/admin/customers/export` |
| Export Sellers | `/admin/sellers/export` |

---

## 🎯 Common Actions

### Approve a New Seller
1. Go to `/admin/sellers`
2. Find seller with "Pending" status
3. Click "View"
4. Click "Approve Seller" button
5. Status changes to "Approved"

### Remove Inactive Customer
1. Go to `/admin/customers`
2. Search for customer name
3. Click "View"
4. Scroll down
5. Click "Delete" button
6. Confirm deletion

### Find All Orders by Customer
1. Go to `/admin/customers`
2. Click on customer name
3. Scroll to "Recent Orders" section
4. View all orders and status

### Check Seller Sales
1. Go to `/admin/sellers`
2. Click on seller name
3. View "Total Sales" stat
4. Scroll to "Recent Products" section

---

## 💡 Tips

- **Use Search First** - Faster than scrolling
- **Date Filters** - Great for monthly reports
- **CSV Export** - Use for analytics and backups
- **Combine Filters** - Search + Date range = powerful
- **Check Status** - Sellers show Pending/Approved
- **Pagination** - 15 items per page, navigate with links

---

## ✅ Checklist for First Use

- [ ] Login to admin account
- [ ] Visit `/admin/customers`
- [ ] Search for a customer
- [ ] View customer details
- [ ] Visit `/admin/sellers`
- [ ] Check seller statuses
- [ ] Approve a pending seller
- [ ] Try exporting data
- [ ] Edit a customer/seller
- [ ] Test filters and search

---

**Ready to manage your platform!** 🚀
