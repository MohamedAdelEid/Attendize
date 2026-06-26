<!DOCTYPE html>
<html >
<head>
    <meta charset="UTF-8">
    <style>
        /* إعدادات الصفحة والاتجاه */
        @page {
            size: A4 landscape;
            margin: 0; /* إزالة الهوامش لتغطية الخلفية لكامل الصفحة */
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        /* تنسيق خلفية الصفحة */
        .background-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
             height: 820px;
            z-index: -1000; /* خلف المحتوى */
        }

        .background-container img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* تغطية المساحة بالكامل */
        }

        /* حاوية المحتوى لتوسيط الأسماء */
        .content {
           position: fixed;
            text-align: center;
			
        }

        .attendee-name {
           
            font-family: 'DejaVu Sans', sans-serif; /* ليدعم العربية إذا لزم */
            font-size: 55px;
            color: #252161;
            font-weight: bold;
			margin-top:305px;
        }
    </style>
</head>
<body>

    <!-- صورة الخلفية -->
    <div class="background-container">
        <!-- استخدم المسار الكامل للصورة (Absolute Path) لضمان ظهورها في الـ PDF -->
        <img src="{{ asset('storage/certificate/certificate.jpg') }}" alt="background">
		  <!-- المحتوى في المنتصف -->
			
    </div>
		<div class="content">
				<div class="attendee-name">
				{{ ucwords(strtolower($attendee_name)) }}
				</div>
			</div>
  

</body>
</html>