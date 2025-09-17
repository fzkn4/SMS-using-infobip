# Infobip SMS API Implementation

A comprehensive demonstration of how to implement Infobip SMS API with PHP and React JS. Features a modern dark-themed web interface for sending SMS messages, real-time preview, delivery tracking, and support for international phone numbers.

![SMS API](https://img.shields.io/badge/SMS-API-green?style=for-the-badge&logo=sms)
![PHP](https://img.shields.io/badge/PHP-8.3+-blue?style=for-the-badge&logo=php)
![Infobip](https://img.shields.io/badge/Infobip-API-orange?style=for-the-badge)

## ✨ Features

- 🌙 **Modern Dark Theme** - Sleek, professional dark mode UI
- 📱 **SMS Sending** - Send SMS messages via official Infobip API
- 🔍 **SMS Preview** - Real-time character counting and message preview
- 📊 **Delivery Reports** - Track message delivery status
- 🌍 **International Support** - Full Unicode and international character support
- 🛠️ **Easy Setup** - Simple local development setup
- 📱 **Mobile Responsive** - Works perfectly on all devices
- ⚡ **Fast & Lightweight** - Optimized for performance

## 🚀 Quick Start

### Prerequisites

- Docker and Docker Compose
- Infobip account and API key

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/infobip-sms-webapp.git
cd infobip-sms-webapp
```

### 2. Configure Environment

```bash
# Edit .env with your Infobip API credentials
# INFOBIP_API_KEY=your_actual_api_key_here
# INFOBIP_BASE_URL=https://your-base-url.api.infobip.com
```

### 3. Start the Application

**Option A: Using the start script (Windows)**

```bash
# Double-click start.bat or run:
./start.bat
```

**Option B: Using Docker Compose directly**

```bash
# Build and start containers
docker-compose up --build
```

### 4. Access the Application

Open your browser and navigate to: **http://localhost:8080**

## 📁 Project Structure

```
infobip-sms-webapp/
├── api/                          # PHP API endpoints
│   ├── send_sms.php             # Send SMS endpoint
│   ├── preview_sms.php          # SMS preview endpoint
│   ├── delivery_reports.php     # Delivery reports endpoint
│   └── error_handler.php        # Error handling utilities
├── src/                         # PHP source code
│   └── SmsService.php           # SMS service class
├── index.html                   # Main web application
├── favicon.svg                  # Application favicon
├── composer.json                # PHP dependencies
├── Dockerfile                   # Docker configuration
├── docker-compose.yml           # Docker Compose configuration
├── start.bat                    # Windows start script
├── .gitignore                   # Git ignore rules
├── LICENSE                      # MIT License
├── CHANGELOG.md                 # Version history
├── CONTRIBUTING.md              # Contribution guidelines
└── README.md                    # This file
```

## 🔧 Configuration

### Environment Variables

| Variable           | Description             | Default                   |
| ------------------ | ----------------------- | ------------------------- |
| `INFOBIP_API_KEY`  | Your Infobip API key    | Required                  |
| `INFOBIP_BASE_URL` | Infobip API base URL    | `https://api.infobip.com` |
| `APP_ENV`          | Application environment | `development`             |

### Phone Number Formats

The application supports international phone number formats:

- ✅ **Correct**: `+639123456789` (with country code)
- ✅ **Also works**: `639123456789` (auto-adds + prefix)
- ❌ **Incorrect**: `09123456789` (local format without country code)

## 📡 API Endpoints

### Send SMS

**POST** `/api/send_sms.php`

Send an SMS message to a phone number.

**Request Body:**

```json
{
  "to": "+639975640228",
  "message": "Hello from Infobip!",
  "from": "MyApp"
}
```

**Response:**

```json
{
  "success": true,
  "message": "SMS sent successfully",
  "data": {
    "bulkId": "17579185614077950423687",
    "messageCount": 1,
    "messages": [...]
  }
}
```

### Preview SMS

**POST** `/api/preview_sms.php`

Preview SMS message to get character count and encoding information.

**Request Body:**

```json
{
  "message": "Hello from Infobip! 🇵🇭"
}
```

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "charactersRemaining": 136,
      "textPreview": "Hello from Infobip! ",
      "characterCount": 24,
      "messageCount": 1
    }
  ]
}
```

### Delivery Reports

**GET** `/api/delivery_reports.php`

Get delivery reports for sent SMS messages.

**Query Parameters:**

- `bulkId` (optional): Filter by bulk ID
- `messageId` (optional): Filter by message ID
- `limit` (optional): Number of reports to return (default: 10)

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "messageId": "msg-id-456",
      "bulkId": "bulk-id-123",
      "to": "+639975640228",
      "status": "DELIVERED",
      "statusDescription": "Message delivered to handset",
      "doneAt": "2024-01-15T10:30:00Z",
      "sentAt": "2024-01-15T10:29:55Z",
      "price": 0.05
    }
  ]
}
```

## 🛠️ Development

### Local Development

1. **Install Docker and Docker Compose**
2. **Configure environment:**
   ```bash
   # Edit .env with your API credentials
   ```
3. **Start with Docker:**
   ```bash
   docker-compose up --build
   ```
4. **Access the app:**
   Open http://localhost:8080 in your browser

### Project Dependencies

- **Docker** - Containerization platform
- **Docker Compose** - Multi-container orchestration
- **PHP 8.3+** - Backend runtime (in container)
- **Composer** - PHP dependency management (in container)
- **Infobip PHP Client** - Official Infobip API client

## 🐛 Troubleshooting

### Common Issues

1. **401 Unauthorized Error**

   - Check your Infobip API key in `.env` file
   - Verify the API key has SMS sending permissions

2. **Dependencies Not Installed**

   - Run `docker-compose up --build` to rebuild with dependencies
   - Check if `vendor/` directory exists in container

3. **Phone Number Format Error**

   - Use international format: `+639975640228`
   - Include country code (e.g., +63 for Philippines)

4. **Docker Issues**
   - Ensure Docker is running
   - Check port availability (8080)
   - View logs: `docker-compose logs`
