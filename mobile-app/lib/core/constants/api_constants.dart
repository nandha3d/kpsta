/// KPSTA REST API Constants
class ApiConstants {
  ApiConstants._();

  static String _customBaseUrl = '';

  static void setBaseUrl(String url) {
    _customBaseUrl = url;
  }

  /// Deployed and active live website API endpoint
  static const String liveAnimazonUrl = 'https://animazon.in/kpsta/index.php/api/v1';
  static const String liveKpstaUrl = 'https://kpsta.in/index.php/api/v1';

  static String get baseUrl {
    const envUrl = String.fromEnvironment('API_BASE_URL');
    if (envUrl.isNotEmpty) {
      return envUrl;
    }
    if (_customBaseUrl.isNotEmpty) {
      return _customBaseUrl;
    }
    // Active deployed live website API
    return liveAnimazonUrl;
  }

  static String get siteUrl {
    if (baseUrl.contains('animazon.in')) {
      return 'https://animazon.in/kpsta';
    }
    return 'https://kpsta.in';
  }

  // Auth endpoints
  static const String login = '/auth/login';
  static const String otpRequest = '/auth/whatsapp/request-otp';
  static const String otpVerify = '/auth/whatsapp/verify-otp';
  static const String tokenRefresh = '/auth/refresh';
  static const String logout = '/auth/logout';
  static const String me = '/auth/me';
  static const String permissions = '/auth/permissions';

  // Public endpoints
  static const String home = '/home';
  static const String news = '/news';
  static const String flashNews = '/flash-news';
  static const String circulars = '/order-circulars';
  static const String circularCategories = '/order-circulars/categories';
  static const String memorandums = '/memorandums';
  static const String downloads = '/downloads';
  static const String downloadCategories = '/downloads/categories';
  static const String gallery = '/galleries';
  static const String reactionGallery = '/reaction-gallery';
  static const String officeBearers = '/office-bearers';
  static const String formerLeaders = '/former-leaders';
  static const String districts = '/districts';
  static const String editorialBoard = '/editorial-board';
  static const String services = '/service-corner';
  static const String adayapaka = '/adayapaka-sabham';
  static const String quickLinks = '/quick-links';
  static const String examResults = '/results';
  static const String contact = '/contact';
  static const String contactEnquiry = '/contact';
  static const String privacy = '/privacy-policy';
  static const String siteVisitors = '/site-visitors';
  static const String donationInitiate = '/donations';
  static const String donationStatus = '/donations';
  static const String registerPushToken = '/notifications/register-token';
  static const String latestNotifications = '/notifications/latest';
  static const String sendPushNotification = '/admin/notifications/send';

  // Admin endpoints
  static const String adminDashboard = '/admin/dashboard';
  static const String adminMediaUpload = '/admin/media/upload';
  static const String adminNews = '/admin/news';
  static const String adminCirculars = '/admin/order-circulars';
  static const String adminDownloads = '/admin/downloads';
  static const String adminOfficeBearers = '/admin/office-bearers';
  static const String adminOfficeBearerMetadata = '/admin/office-bearers/metadata';
  static const String adminFlashNews = '/admin/flash-news';
  static const String adminSliders = '/admin/sliders';
  static const String adminGalleries = '/admin/galleries';
  static const String adminQuickLinks = '/admin/quick-links';
  static const String adminResultLinks = '/admin/result-links';
  static const String adminSettings = '/admin/settings';

  // Membership endpoints
  static const String membershipDashboard = '/membership/dashboard';
  static const String membershipCounts = '/membership/counts';
  static const String membershipReports = '/membership/reports';
  static const String membershipMetadata = '/membership/metadata';
  static const String membershipTeachers = '/membership/teachers';
  static const String membershipWhatsNew = '/membership/whats-new';
}
