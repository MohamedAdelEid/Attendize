// Global variables
let currentStream = null
let isScanning = false
const registrations = []
const filteredRegistrations = []

// Import jsQR library
const jsQR = window.jsQR

// Get event ID and CSRF token from the page
const eventId = document.querySelector('meta[name="event-id"]')?.getAttribute("content")
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content")

// Initialize the application
document.addEventListener("DOMContentLoaded", () => {
  loadRegistrations()
  updateStats()
})

// Tab Management
function showTab(tabName) {
  // Hide all tabs
  document.getElementById("scannerTab").classList.add("hidden")
  document.getElementById("dashboardTab").classList.add("hidden")

  // Show selected tab
  document.getElementById(tabName + "Tab").classList.remove("hidden")

  // Update navigation - desktop
  document.querySelectorAll(".nav-btn").forEach((btn) => {
    btn.classList.remove("active", "bg-black", "text-white")
    btn.classList.add("text-gray-700", "hover:text-black", "hover:bg-gray-100")
  })

  // Set active state
  event.target.classList.add("active", "bg-black", "text-white")
  event.target.classList.remove("text-gray-700", "hover:text-black", "hover:bg-gray-100")

  // Stop camera when switching away from scanner
  if (tabName !== "scanner" && currentStream) {
    stopCamera()
  }
}

// Camera Functions
async function startCamera() {
  try {
    const constraints = {
      video: {
        facingMode: "environment",
        width: { ideal: 640 },
        height: { ideal: 480 },
      },
    }

    currentStream = await navigator.mediaDevices.getUserMedia(constraints)
    const video = document.getElementById("qrVideo")
    video.srcObject = currentStream

    // Show video, hide placeholder
    video.classList.remove("hidden")
    document.getElementById("scannerPlaceholder").classList.add("hidden")
    document.getElementById("scannerOverlay").classList.remove("hidden")

    // Update buttons
    document.getElementById("startCameraBtn").classList.add("hidden")
    document.getElementById("stopCameraBtn").classList.remove("hidden")

    // Start scanning
    isScanning = true
    scanQRCode()

    showToast("Camera started successfully", "success")
  } catch (error) {
    console.error("Error accessing camera:", error)
    showToast("Unable to access camera. Please check permissions.", "error")
  }
}

function stopCamera() {
  if (currentStream) {
    currentStream.getTracks().forEach((track) => track.stop())
    currentStream = null
  }

  isScanning = false

  // Hide video, show placeholder
  document.getElementById("qrVideo").classList.add("hidden")
  document.getElementById("scannerPlaceholder").classList.remove("hidden")
  document.getElementById("scannerOverlay").classList.add("hidden")

  // Update buttons
  document.getElementById("startCameraBtn").classList.remove("hidden")
  document.getElementById("stopCameraBtn").classList.add("hidden")
}

// QR Code Scanning
function scanQRCode() {
  if (!isScanning) return

  const video = document.getElementById("qrVideo")
  const canvas = document.getElementById("qrCanvas")
  const context = canvas.getContext("2d")

  if (video.readyState === video.HAVE_ENOUGH_DATA) {
    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    context.drawImage(video, 0, 0, canvas.width, canvas.height)

    const imageData = context.getImageData(0, 0, canvas.width, canvas.height)
    const code = jsQR(imageData.data, imageData.width, imageData.height)

    if (code) {
      handleQRCodeDetected(code.data)
      return
    }
  }

  requestAnimationFrame(scanQRCode)
}

function handleQRCodeDetected(qrData) {
  isScanning = false
  const uniqueCode = qrData.trim().toUpperCase()
  performCheckIn(uniqueCode)

  setTimeout(() => {
    if (currentStream) {
      isScanning = true
      scanQRCode()
    }
  }, 2000)
}

// Manual Check-in
function handleManualCheckIn(event) {
  event.preventDefault()
  const uniqueCode = document.getElementById("uniqueCode").value.trim().toUpperCase()
  if (!uniqueCode) return
  performCheckIn(uniqueCode)
}

// Check-in/Check-out Logic - Updated for multiple attendance records
async function performCheckIn(uniqueCode) {
  const btn = document.getElementById("manualCheckInBtn")
  const originalHTML = btn.innerHTML

  // Show loading state
  btn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mx-auto"></div>'
  btn.disabled = true

  try {
    const result = await callCheckInAPI(uniqueCode)
    displayCheckInResult(result)

    if (result.status === "success") {
      document.getElementById("uniqueCode").value = ""
      updateStats()
      if (!document.getElementById("dashboardTab").classList.contains("hidden")) {
        loadRegistrations()
      }
    }
  } catch (error) {
    console.error("Check-in error:", error)
    displayCheckInResult({
      status: "error",
      message: "Network error. Please try again.",
    })
  } finally {
    btn.innerHTML = originalHTML
    btn.disabled = false
  }
}

// API call to Laravel backend
async function callCheckInAPI(uniqueCode) {
  const formData = new FormData()
  formData.append("unique_code", uniqueCode)
  formData.append("_token", csrfToken)

  const response = await fetch(`/events/${eventId}/post-scan-ticket`, {
    method: "POST",
    body: formData,
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })

  if (!response.ok) {
    throw new Error("Network response was not ok")
  }

  return await response.json()
}

// Display Check-in/Check-out Result - Enhanced for attendance history
function displayCheckInResult(result) {
  const resultDiv = document.getElementById("checkInResult")
  const isSuccess = result.status === "success"
  const action = result.action || "check_in"

  // Different colors and icons for check-in vs check-out
  const actionConfig = {
    check_in: {
      bgColor: "bg-green-50 border-green-400",
      textColor: "text-green-800",
      iconColor: "text-green-400",
      icon: "fa-sign-in-alt",
      actionText: "Checked In",
    },
    check_out: {
      bgColor: "bg-blue-50 border-blue-400",
      textColor: "text-blue-800",
      iconColor: "text-blue-400",
      icon: "fa-sign-out-alt",
      actionText: "Checked Out",
    },
  }

  const config = actionConfig[action] || actionConfig.check_in
  const errorConfig = "bg-red-50 border-red-400"
  const errorTextColor = "text-red-800"
  const errorIconColor = "text-red-400"

  let attendanceHistoryHtml = ""
  if (result.user && result.user.attendance_history && result.user.attendance_history.length > 0) {
    attendanceHistoryHtml = `
      <div class="mt-3 p-3 bg-gray-50 rounded-lg">
        <h4 class="text-xs font-medium text-gray-700 mb-2">Recent Attendance History:</h4>
        <div class="space-y-1">
          ${result.user.attendance_history
            .slice(0, 3)
            .map(
              (attendance) => `
            <div class="text-xs text-gray-600 flex items-center space-x-2">
              <i class="fas ${attendance.status === "checked_in" ? "fa-sign-in-alt text-green-500" : "fa-sign-out-alt text-blue-500"}"></i>
              <span>${attendance.status === "checked_in" ? "Check-in" : "Check-out"}: ${new Date(attendance.check_in || attendance.check_out).toLocaleString()}</span>
            </div>
          `,
            )
            .join("")}
        </div>
      </div>
    `
  }

  const html = `
    <div class="p-4 rounded-lg border-l-4 ${isSuccess ? config.bgColor : errorConfig} animate-fade-in">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <i class="fas ${isSuccess ? config.icon : "fa-times-circle"} ${isSuccess ? config.iconColor : errorIconColor} text-xl"></i>
        </div>
        <div class="ml-3 flex-1">
          <p class="text-sm font-medium ${isSuccess ? config.textColor : errorTextColor}">
            ${result.message}
          </p>
          ${
            result.user && isSuccess
              ? `
            <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 ${isSuccess ? config.bgColor.split(" ")[0] : "bg-gray-100"} rounded-full flex items-center justify-center">
                    <i class="fas ${config.icon} ${config.iconColor}"></i>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900">
                    ${result.user.first_name || ""} ${result.user.last_name || ""}
                  </p>
                  <p class="text-sm text-gray-500">${result.user.email || ""}</p>
                  <p class="text-xs ${config.textColor} mt-1">
                    <i class="fas fa-clock mr-1"></i>
                    ${config.actionText} at ${new Date().toLocaleTimeString()}
                  </p>
                  <div class="text-xs text-gray-600 mt-1">
                    Current Status: <span class="font-medium">${result.user.current_status}</span>
                  </div>
                </div>
              </div>
              ${attendanceHistoryHtml}
            </div>
          `
              : ""
          }
        </div>
      </div>
    </div>
  `

  resultDiv.innerHTML = html
  resultDiv.classList.remove("hidden")

  // Show different toast messages for different actions
  const toastMessage = isSuccess
    ? action === "check_out"
      ? "Successfully checked out!"
      : "Successfully checked in!"
    : result.message

  showToast(toastMessage, result.status)

  setTimeout(() => {
    resultDiv.classList.add("hidden")
  }, 8000) // Longer display time for attendance history
}

// Registration Management
function loadRegistrations() {
  // This would be implemented to load registration data via AJAX
  // For now, we'll use the data already loaded in the page
}

function updateStats() {
  // Stats are now calculated server-side and displayed in the blade template
  // This function can be used to refresh stats via AJAX if needed
}

function filterRegistrations() {
  // Implementation for filtering the registration table
  const searchTerm = document.getElementById("searchInput").value.toLowerCase()
  const statusFilter = document.getElementById("statusFilter").value

  const rows = document.querySelectorAll("tbody tr")

  rows.forEach((row) => {
    const name = row.querySelector("td:first-child").textContent.toLowerCase()
    const email = row.querySelector("td:nth-child(2)").textContent.toLowerCase()
    const code = row.querySelector("td:nth-child(3)").textContent.toLowerCase()

    const matchesSearch =
      !searchTerm || name.includes(searchTerm) || email.includes(searchTerm) || code.includes(searchTerm)

    let matchesStatus = true
    if (statusFilter) {
      const statusCell = row.querySelector("td:nth-child(4)")
      const statusText = statusCell.textContent.toLowerCase()

      switch (statusFilter) {
        case "checked_in":
          matchesStatus = statusText.includes("checked in")
          break
        case "checked_out":
          matchesStatus = statusText.includes("checked out")
          break
        case "never_attended":
          matchesStatus = statusText.includes("never attended")
          break
      }
    }

    if (matchesSearch && matchesStatus) {
      row.style.display = ""
    } else {
      row.style.display = "none"
    }
  })
}

// UI Helper Functions
function showToast(message, type = "success") {
  const container = document.getElementById("toastContainer")
  const toastId = Date.now()
  const isSuccess = type === "success"
  const bgColor = isSuccess ? "bg-green-600" : "bg-red-600"
  const icon = isSuccess ? "fa-check-circle" : "fa-exclamation-circle"

  const toast = document.createElement("div")
  toast.id = `toast-${toastId}`
  toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 animate-slide-up max-w-sm`
  toast.innerHTML = `
    <i class="fas ${icon} text-lg"></i>
    <span class="font-medium">${message}</span>
    <button onclick="removeToast('toast-${toastId}')" class="ml-auto text-white hover:text-gray-200">
      <i class="fas fa-times"></i>
    </button>
  `

  container.appendChild(toast)

  setTimeout(() => {
    removeToast(`toast-${toastId}`)
  }, 5000)
}

function removeToast(toastId) {
  const toast = document.getElementById(toastId)
  if (toast) {
    toast.style.opacity = "0"
    toast.style.transform = "translateX(100%)"
    setTimeout(() => {
      toast.remove()
    }, 300)
  }
}

function showLoadingModal() {
  document.getElementById("loadingModal").classList.remove("hidden")
}

function hideLoadingModal() {
  document.getElementById("loadingModal").classList.add("hidden")
}
