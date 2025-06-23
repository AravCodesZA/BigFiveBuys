BigFiveBuys  
A secure and user-friendly South African C2C e-commerce platform built to empower local entrepreneurs, informal sellers, and digital buyers.

🔧 Tech Stack

- **Frontend:** HTML, CSS, JavaScript (jQuery)
- **Backend:** PHP (Vanilla PHP, no CMS)
- **Database:** MySQL
- **Frameworks & Tools:** Bootstrap 5, FontAwesome, AOS (Animate on Scroll)


✅ Features

- 🔐 **User Authentication**: Secure login/registration with role-based access (`admin` and `user`)
- 🛍️ **Product Management**: Post, update, browse, and view products with live search and filters
- 🧑‍💼 **Admin Dashboard**: Admin control panel to manage users, products, and generate reports
- 💬 **Buyer–Seller Interaction**: Simulated messaging (foundation built for future integration)
- 📷 **Image Uploads**: Product images stored securely in `/uploads/`, validated on upload
- 🔎 **Search & Filter**: Dynamic product search, min/max price filters
- 📱 **Mobile-Friendly**: Responsive and optimized for all screen sizes
- 🧾 **Report Generation**: Includes dummy data for admin reporting and printable invoices


🛡️ Security Features

- ✅ **Session Management**: Role-based access control via `$_SESSION`
- ✅ **SQL Injection Protection**: All inserts use `mysqli_prepare()` and `bind_param()`
- ✅ **XSS Protection**: All form inputs and outputs sanitized using `htmlspecialchars()` and `mysqli_real_escape_string()`
- ✅ **File Upload Safety**: Only image MIME types accepted; uploaded via `move_uploaded_file()` into a locked `uploads/` directory
- ✅ **Access Restrictions**: Only verified sellers can post products, and only logged-in users can browse/view fully
- ✅ **Error Handling**: Full error visibility in development; silent logging ready for production


📁 Deliverables

- ✅ System Design Diagrams: CRC Cards, Enhanced ERD, Context Diagram, DFD, Use Case Diagram
- ✅ Wireframes: Custom UI sketches for user flow and admin UX
- ✅ Complete Source Code: Frontend and backend with organized logic
- ✅ Admin Features: Edit/Delete user or product, generate reports, moderate listings
- ✅ Dummy Data: Auto-filled test accounts and products
- ✅ Hosting & User Manual: Fully deployed and documented on InfinityFree

🎯 Project Goal

BigFiveBuys was designed to address the **growing distrust and digital exclusion** within South Africa’s informal online market. With 70%+ of consumers citing fraud and scam concerns on C2C platforms, this site offers:

- Verified seller workflows  
- Transparent listings with structured feedback  
- A locally hosted, community-owned alternative to OLX/Facebook Marketplace  

The aim is to **democratize access to digital trade** and **stimulate the informal economy** with tech that is easy to use, trustworthy, and scalable.


📆 Timeline (Key Milestones)

| Phase                | Tasks                                            | Dates                |
|---------------------|--------------------------------------------------|----------------------|
| 🧠 Research & Planning | Problem identification, requirement gathering   | 3 Feb – 28 Feb, 2025 |
| 🛠️ Design & Development | UI/UX, database schema, core coding             | 28 Feb – 6 Jun, 2025 |
| 🚀 Deployment & Testing | Hosting, user manual, final presentation        | 6 Jun – 30 Jun, 2025  |


🌐 Hosting

Live demo hosted via **InfinityFree**  
🌍 [https://bigfivebuys.infinityfreeapp.com](https://bigfivebuys.infinityfreeapp.com)


🧾 License

This project is created for **educational purposes only** under Eduvos academic submission guidelines.  
No commercial use is permitted without permission.


🙌 Acknowledgments

- Research inspired by:  
  *Ruttell, B. (2018).* *Buyers’ trust and seller verification in South African C2C platforms.*
- Faculty guidance and infrastructure by **Eduvos**, Department of Information Technology.


📣 Contact

**Developer:** Arav Baboolal  
**Email:** aravbaboolal22@gmail.com  
**Institution:** Eduvos  
**Module:** ITECA3-12 (Web Development & E-Commerce)
