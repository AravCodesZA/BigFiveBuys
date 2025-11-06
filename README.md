# BigFiveBuys

A secure, responsive, and user-friendly South African **C2C e-commerce platform**, built to empower local entrepreneurs, informal traders, and digital buyers. Designed with simplicity and trust at its core, BigFiveBuys offers a modern alternative to OLX and Facebook Marketplace which is owned, hosted, and operated locally.

---

## 🔧 Tech Stack

- **Frontend:** HTML, CSS, JavaScript (jQuery)  
- **Backend:** PHP (Vanilla PHP, no CMS)  
- **Database:** MySQL  
- **Frameworks & Tools:** Bootstrap 5, FontAwesome, AOS (Animate on Scroll)

---

## ✅ Key Features

- 🔐 **Authentication:** Secure login/registration with role-based access (`admin`, `user`)  
- 🛍️ **Product Management:** Post, update, browse, and view products with advanced filtering  
- 🧑‍💼**Admin Dashboard:** Manage users, products, and generate downloadable reports  
- 💬 **Communication Ready:** Simulated buyer–seller chat foundation  
- 📷 **Image Uploads:** Secure upload with file validation  
- 🔎 **Search & Filter:** Keyword, min/max price filters  
- 📱 **Mobile-Responsive UI:** Seamless experience on all devices  
- 🧾 **Admin Reporting:** Downloadable invoices and dummy data for testing

---

## 🛡️ Security Features

- ✅ **Session-Based Access:** Pages protected using `$_SESSION` checks  
- ✅ **SQL Injection Prevention:** All inputs passed via `mysqli_prepare()` and `bind_param()`  
- ✅ **XSS Prevention:** All user inputs sanitized with `htmlspecialchars()` and `mysqli_real_escape_string()`  
- ✅ **Upload Protection:** Only image MIME types accepted, securely moved to `/uploads/`  
- ✅ **Seller Verification:** Only verified sellers can post products  
- ✅ **Error Control:** Developer-mode error reporting; production-safe fallback ready

---

## 📁 Project Deliverables

- ✅ Full UI/UX wireframes and layout plans  
- ✅ System Design Diagrams (CRC, EERD, DFD, Use Case, Context Diagram)  
- ✅ Fully functional front-end and back-end  
- ✅ Admin control panel with CRUD and reporting tools  
- ✅ Preloaded dummy data for demonstration  
- ✅ Complete user manual and live deployment

---

## 🎯 Project Objective

This platform addresses widespread **distrust in online consumer-to-consumer platforms** in South Africa. It aims to:

- Enable safer peer-to-peer trade  
- Offer a local alternative to foreign-owned marketplaces  
- Improve seller credibility and verification  
- Encourage digital participation from informal businesses  

With over **70% of South African users citing fraud as a key barrier to e-commerce**, BigFiveBuys fills a crucial gap by **restoring trust** and keeping value within the local economy.

---

## 📆 Timeline

| Phase                   | Task Overview                                  | Dates                |
|------------------------|-------------------------------------------------|----------------------|
| 🧠 Planning             | Problem definition, scope, goal setting         | 3 Feb – 28 Feb 2025  |
| 🛠️ Design & Development | UI, DB schema, full code implementation         | 28 Feb – 6 Jun 2025  |
| 🚀 Deployment           | Hosting, user testing, presentation delivery    | 6 Jun – 30 Jun 2025  |

---

## 🌐 Live Demo

Hosted on **InfinityFree**  
🌍 [https://bigfivebuys.infinityfreeapp.com](https://bigfivebuys.infinityfreeapp.com)

NOTE: When you click on this link, you will recieve a security error due to Google's safe browsing.
To view the webiste, you can do so by using these steps:

Copy the URL, then:

Open a private/incognito window.

Type: chrome://settings/security

Under Safe Browsing, temporarily switch from “Enhanced” to “No protection (not recommended)”.

Reload the site.

If it works, immediately switch Safe Browsing back on once you’re done.
---

## 🧾 License

This project was created for **academic and educational purposes only**, in accordance with **Eduvos** assessment policies.  
Use or distribution outside this context requires permission.

---

## 🙌 Acknowledgments

- Research: *Ruttell, B. (2018). Buyers’ trust and seller verification in South African C2C platforms.*  
- Academic support provided by the **Eduvos Faculty of Information Technology**

---

## 📣 Contact

**Developer:** Arav Baboolal  
📧 **Email:** aravbaboolal22@gmail.com  
🏫 **Institution:** Eduvos  
📘 **Module:** ITECA3-12 – Web Development & E-Commerce
