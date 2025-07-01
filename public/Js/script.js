// Global variables
let currentStream = null
let isScanning = false
let registrations = []
let filteredRegistrations = []

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

  // Update navigation - mobile
  document.querySelectorAll(".mobile-nav-btn").forEach((btn) => {
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

function toggleMobileMenu() {
  const mobileMenu = document.getElementById("mobileMenu")
  mobileMenu.classList.toggle("hidden")
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

// Check-in Logic - Modified to use actual Laravel backend
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

// Real API call to Laravel backend
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

  // Handle both JSON and redirect responses
  const contentType = response.headers.get("content-type")

  if (contentType && contentType.includes("application/json")) {
    return await response.json()
  } else {
    // Handle redirect response (Laravel's back() with session data)
    const text = await response.text()

    // Parse the response to extract session data
    // This is a simplified approach - you might need to adjust based on your needs
    if (text.includes("User Checked In Successfully")) {
      return {
        status: "success",
        message: "Successfully checked in!",
        user: {
          // You might need to extract user data from the response
          // or make another API call to get user details
        },
      }
    } else if (text.includes("Invalid QR Code")) {
      return {
        status: "error",
        message: "Invalid ticket code. Please check the code and try again.",
      }
    } else if (text.includes("User Already Checked In")) {
      return {
        status: "error",
        message: "Attendee already checked in",
      }
    } else {
      return {
        status: "error",
        message: "An error occurred. Please try again.",
      }
    }
  }
}

// Display Check-in Result
function displayCheckInResult(result) {
  const resultDiv = document.getElementById("checkInResult")
  const isSuccess = result.status === "success"

  const html = `
    <div class="p-4 rounded-lg border-l-4 ${isSuccess ? "bg-green-50 border-green-400" : "bg-red-50 border-red-400"} animate-fade-in">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <i class="fas ${isSuccess ? "fa-check-circle text-green-400" : "fa-times-circle text-red-400"} text-xl"></i>
        </div>
        <div class="ml-3 flex-1">
          <p class="text-sm font-medium ${isSuccess ? "text-green-800" : "text-red-800"}">
            ${result.message}
          </p>
          ${
            result.user
              ? `
            <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-gray-600"></i>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900">
                    ${result.user.first_name || ""} ${result.user.last_name || ""}
                  </p>
                  <p class="text-sm text-gray-500">${result.user.email || ""}</p>
                  <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-clock mr-1"></i>
                    Checked in at ${new Date().toLocaleTimeString()}
                  </p>
                </div>
              </div>
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
  showToast(result.message, result.status)

  setTimeout(() => {
    resultDiv.classList.add("hidden")
  }, 5000)
}

// Registration Management
function loadRegistrations() {
  // Use vanilla JavaScript instead of jQuery
  const xhr = new XMLHttpRequest()
  xhr.open("GET", `/events/${eventId}/fetch-registration-users`, true)
  xhr.onreadystatechange = () => {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      const data = JSON.parse(xhr.responseText)
      registrations = data.registrationUsers
      console.log(registrations)
      filteredRegistrations = [...registrations]
      renderRegistrationsTable()
      updateStats()
    } else if (xhr.readyState === XMLHttpRequest.DONE) {
      console.error("Error loading registrations:", xhr.statusText)
    }
  }
  xhr.send()
}

function getActionButtons(registration) {
  const buttons = []

  if (registration.status === "pending") {
    buttons.push(`
      <button onclick="approveRegistration(${registration.id})" 
              class="bg-green-600 text-white px-3 py-1 rounded text-xs font-medium hover:bg-green-700 transition-colors duration-200 mr-2">
        <i class="fas fa-check mr-1"></i>Approve
      </button>
      <button onclick="rejectRegistration(${registration.id})" 
              class="bg-red-600 text-white px-3 py-1 rounded text-xs font-medium hover:bg-red-700 transition-colors duration-200">
        <i class="fas fa-times mr-1"></i>Reject
      </button>
    `)
  } else if (registration.status === "approved" && !registration.ticket_generated_at) {
    buttons.push(`
      <button onclick="generateTicket(${registration.id})" 
              class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium hover:bg-blue-700 transition-colors duration-200">
        <i class="fas fa-qrcode mr-1"></i>Generate Ticket
      </button>
    `)
  } else if (registration.ticket_generated_at) {
    buttons.push(`
      <button onclick="downloadTicket(${registration.id})" 
              class="bg-gray-600 text-white px-3 py-1 rounded text-xs font-medium hover:bg-gray-700 transition-colors duration-200">
        <i class="fas fa-download mr-1"></i>Download
      </button>
    `)
  }

  return `<div class="flex items-center space-x-2">${buttons.join("")}</div>`
}

// Registration Actions
async function approveRegistration(id) {
  showLoadingModal()
  try {
    await new Promise((resolve) => setTimeout(resolve, 1000))
    const registration = registrations.find((reg) => reg.id === id)
    if (registration) {
      registration.status = "approved"
      registration.unique_code = generateUniqueCode()
      registration.updated_at = new Date().toISOString()
    }
    renderRegistrationsTable()
    updateStats()
    showToast("Registration approved successfully", "success")
  } catch (error) {
    showToast("Error approving registration", "error")
  } finally {
    hideLoadingModal()
  }
}

async function rejectRegistration(id) {
  showLoadingModal()
  try {
    await new Promise((resolve) => setTimeout(resolve, 1000))
    const registration = registrations.find((reg) => reg.id === id)
    if (registration) {
      registration.status = "rejected"
      registration.updated_at = new Date().toISOString()
    }
    renderRegistrationsTable()
    updateStats()
    showToast("Registration rejected", "success")
  } catch (error) {
    showToast("Error rejecting registration", "error")
  } finally {
    hideLoadingModal()
  }
}

// Utility Functions
function generateUniqueCode() {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
  let result = ""
  for (let i = 0; i < 6; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length))
  }
  return result
}

function filterRegistrations() {
  const searchTerm = document.getElementById("searchInput").value.toLowerCase()
  const statusFilter = document.getElementById("statusFilter").value

  filteredRegistrations = registrations.filter((reg) => {
    const matchesSearch =
      !searchTerm ||
      reg.first_name.toLowerCase().includes(searchTerm) ||
      reg.last_name.toLowerCase().includes(searchTerm) ||
      reg.email.toLowerCase().includes(searchTerm) ||
      (reg.unique_code && reg.unique_code.toLowerCase().includes(searchTerm))

    const matchesStatus = !statusFilter || reg.status === statusFilter

    return matchesSearch && matchesStatus
  })

  renderRegistrationsTable()
}

function refreshRegistrations() {
  showLoadingModal()
  setTimeout(() => {
    loadRegistrations()
    updateStats()
    hideLoadingModal()
    showToast("Registrations refreshed", "success")
  }, 1000)
}

function updateStats() {
  const total = registrations.length
  const checkedIn = registrations.filter((reg) => reg.check_in).length
  const qrGenerated = registrations.filter((reg) => reg.ticket_generated_at).length
  const pending = registrations.filter((reg) => reg.status === "pending").length

  document.getElementById("totalRegistrations").textContent = total
  document.getElementById("checkedIn").textContent = checkedIn
  document.getElementById("qrGenerated").textContent = qrGenerated
  document.getElementById("pendingApproval").textContent = pending
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

// Add this function if renderRegistrationsTable is missing
function renderRegistrationsTable() {
  // Implementation depends on your table structure
  // This is a placeholder - you'll need to implement based on your needs
  console.log("Rendering registrations table with", filteredRegistrations.length, "items")
}
