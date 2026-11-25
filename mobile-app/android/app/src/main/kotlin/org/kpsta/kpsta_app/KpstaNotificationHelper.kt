package org.kpsta.kpsta_app

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.app.job.JobInfo
import android.app.job.JobScheduler
import android.content.ComponentName
import android.content.Context
import android.content.Intent
import android.graphics.BitmapFactory
import android.os.Build

object KpstaNotificationHelper {
    private const val PREFS_NAME = "kpsta_sync_prefs"
    private const val KEY_SEEN_IDS = "seen_ids"
    private const val KEY_API_BASE = "api_base_url"
    private const val CHANNEL_ID = "kpsta_orders_news"
    private const val JOB_ID = 4109
    private const val MAX_SEEN = 60

    const val EXTRA_TARGET_URL = "kpsta_target_url"
    const val DEFAULT_API_BASE = "https://animazon.in/kpsta/index.php/api/v1"

    fun saveApiBaseUrl(context: Context, apiBaseUrl: String) {
        if (apiBaseUrl.isBlank()) return
        context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
            .edit()
            .putString(KEY_API_BASE, apiBaseUrl.trimEnd('/'))
            .apply()
    }

    fun getApiBaseUrl(context: Context): String {
        return context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
            .getString(KEY_API_BASE, DEFAULT_API_BASE)
            ?.ifBlank { DEFAULT_API_BASE } ?: DEFAULT_API_BASE
    }

    fun getSeenIds(context: Context): MutableList<String> {
        val raw = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
            .getString(KEY_SEEN_IDS, "") ?: ""
        if (raw.isBlank()) return mutableListOf()
        return raw.split("|").map { it.trim() }.filter { it.isNotEmpty() }.toMutableList()
    }

    fun saveSeenIds(context: Context, ids: List<String>) {
        val uniqueTrimmed = ids.map { it.trim() }
            .filter { it.isNotEmpty() }
            .distinct()
            .take(MAX_SEEN)
        context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
            .edit()
            .putString(KEY_SEEN_IDS, uniqueTrimmed.joinToString("|"))
            .apply()
    }

    fun ensureChannel(context: Context) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val manager = context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            val existing = manager.getNotificationChannel(CHANNEL_ID)
            if (existing == null) {
                val channel = NotificationChannel(
                    CHANNEL_ID,
                    "KPSTA Orders & News",
                    NotificationManager.IMPORTANCE_HIGH
                ).apply {
                    description = "Alerts when official Government Orders, Circulars or News are uploaded on KPSTA website."
                    enableVibration(true)
                }
                manager.createNotificationChannel(channel)
            }
        }
    }

    fun showNotification(
        context: Context,
        notifKey: String,
        title: String,
        body: String,
        targetUrl: String
    ) {
        ensureChannel(context)
        val manager = context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager

        val intent = Intent(context, MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_SINGLE_TOP or Intent.FLAG_ACTIVITY_CLEAR_TOP
            putExtra(EXTRA_TARGET_URL, targetUrl)
        }

        val reqCode = notifKey.hashCode()
        val pendingFlags = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        } else {
            PendingIntent.FLAG_UPDATE_CURRENT
        }
        val pendingIntent = PendingIntent.getActivity(context, reqCode, intent, pendingFlags)

        val builder = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            Notification.Builder(context, CHANNEL_ID)
        } else {
            @Suppress("DEPRECATION")
            Notification.Builder(context).setPriority(Notification.PRIORITY_HIGH)
        }

        val largeIcon = BitmapFactory.decodeResource(context.resources, R.mipmap.ic_launcher)

        builder
            .setSmallIcon(R.drawable.ic_kpsta_notify)
            .setLargeIcon(largeIcon)
            .setContentTitle(title)
            .setContentText(body)
            .setStyle(Notification.BigTextStyle().bigText(body))
            .setAutoCancel(true)
            .setContentIntent(pendingIntent)

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            builder.setColor(0xFF016D77.toInt())
        }

        manager.notify(reqCode, builder.build())
    }

    fun schedulePeriodicSync(context: Context) {
        val scheduler = context.getSystemService(Context.JOB_SCHEDULER_SERVICE) as? JobScheduler ?: return
        val serviceName = ComponentName(context, KpstaNotificationJobService::class.java)

        val jobBuilder = JobInfo.Builder(JOB_ID, serviceName)
            .setRequiredNetworkType(JobInfo.NETWORK_TYPE_ANY)
            .setPersisted(true)
            .setPeriodic(15 * 60 * 1000L) // Android minimum background period (15 minutes)

        scheduler.schedule(jobBuilder.build())
    }
}

