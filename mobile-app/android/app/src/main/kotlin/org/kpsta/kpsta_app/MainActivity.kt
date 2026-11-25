package org.kpsta.kpsta_app

import android.Manifest
import android.content.Intent
import android.content.pm.PackageManager
import android.os.Build
import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.MethodChannel

class MainActivity : FlutterActivity() {
    companion object {
        private const val CHANNEL = "org.kpsta.kpsta_app/notifications"
        private const val REQ_POST_NOTIFICATIONS = 4108
    }

    private var notificationChannel: MethodChannel? = null
    private var pendingRoute: String? = null

    override fun configureFlutterEngine(flutterEngine: FlutterEngine) {
        super.configureFlutterEngine(flutterEngine)

        extractRouteFromIntent(intent)

        notificationChannel = MethodChannel(flutterEngine.dartExecutor.binaryMessenger, CHANNEL)
        notificationChannel?.setMethodCallHandler { call, result ->
            when (call.method) {
                "initializeSync" -> {
                    val apiBaseUrl = call.argument<String>("apiBaseUrl") ?: ""
                    KpstaNotificationHelper.saveApiBaseUrl(applicationContext, apiBaseUrl)
                    KpstaNotificationHelper.ensureChannel(applicationContext)
                    requestPostNotificationsIfNeeded()
                    KpstaNotificationHelper.schedulePeriodicSync(applicationContext)
                    result.success(true)
                }
                "markSeenIds" -> {
                    val ids = call.argument<List<String>>("ids") ?: emptyList()
                    KpstaNotificationHelper.saveSeenIds(applicationContext, ids)
                    result.success(true)
                }
                "showSystemNotification" -> {
                    val id = call.argument<String>("id") ?: "kpsta_${System.currentTimeMillis()}"
                    val title = call.argument<String>("title") ?: "KPSTA Update"
                    val body = call.argument<String>("body") ?: ""
                    val targetUrl = call.argument<String>("targetUrl") ?: "/home"
                    KpstaNotificationHelper.showNotification(
                        applicationContext,
                        id,
                        title,
                        body,
                        targetUrl
                    )
                    result.success(true)
                }
                "consumeInitialRoute" -> {
                    val route = pendingRoute
                    pendingRoute = null
                    result.success(route)
                }
                else -> result.notImplemented()
            }
        }
    }

    override fun onNewIntent(intent: Intent) {
        super.onNewIntent(intent)
        setIntent(intent)
        val route = intent.getStringExtra(KpstaNotificationHelper.EXTRA_TARGET_URL)
        if (!route.isNullOrBlank()) {
            notificationChannel?.invokeMethod("onNotificationTapped", route)
        }
    }

    private fun extractRouteFromIntent(intent: Intent?) {
        val route = intent?.getStringExtra(KpstaNotificationHelper.EXTRA_TARGET_URL)
        if (!route.isNullOrBlank()) {
            pendingRoute = route
            intent.removeExtra(KpstaNotificationHelper.EXTRA_TARGET_URL)
        }
    }

    private fun requestPostNotificationsIfNeeded() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (checkSelfPermission(Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
                requestPermissions(arrayOf(Manifest.permission.POST_NOTIFICATIONS), REQ_POST_NOTIFICATIONS)
            }
        }
    }
}
