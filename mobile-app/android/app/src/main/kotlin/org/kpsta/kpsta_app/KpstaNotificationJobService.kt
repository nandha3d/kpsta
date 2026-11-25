package org.kpsta.kpsta_app

import android.app.job.JobParameters
import android.app.job.JobService
import org.json.JSONObject
import java.io.BufferedReader
import java.io.InputStreamReader
import java.net.HttpURLConnection
import java.net.URL
import java.util.concurrent.Executors

class KpstaNotificationJobService : JobService() {
    private val executor = Executors.newSingleThreadExecutor()

    override fun onStartJob(params: JobParameters?): Boolean {
        val appContext = applicationContext
        executor.execute {
            var needsReschedule = false
            try {
                val baseUrl = KpstaNotificationHelper.getApiBaseUrl(appContext)
                val endpoint = URL("$baseUrl/notifications/latest")
                val conn = (endpoint.openConnection() as HttpURLConnection).apply {
                    requestMethod = "GET"
                    connectTimeout = 8000
                    readTimeout = 8000
                    setRequestProperty("Accept", "application/json")
                }

                if (conn.responseCode in 200..299) {
                    val bodyText = BufferedReader(InputStreamReader(conn.inputStream)).use { it.readText() }
                    val json = JSONObject(bodyText)
                    val data = json.optJSONArray("data")
                    if (data != null && data.length() > 0) {
                        val seenIds = KpstaNotificationHelper.getSeenIds(appContext)
                        val incomingIds = mutableListOf<String>()

                        if (seenIds.isEmpty()) {
                            // First-ever background initialization: record existing items quietly
                            for (i in 0 until data.length()) {
                                val item = data.optJSONObject(i) ?: continue
                                val id = item.optString("id", "")
                                if (id.isNotEmpty()) incomingIds.add(id)
                            }
                            KpstaNotificationHelper.saveSeenIds(appContext, incomingIds)
                        } else {
                            var alertCount = 0
                            for (i in 0 until data.length()) {
                                val item = data.optJSONObject(i) ?: continue
                                val id = item.optString("id", "")
                                if (id.isEmpty()) continue
                                incomingIds.add(id)

                                if (!seenIds.contains(id) && alertCount < 3) {
                                    val title = item.optString("title", "KPSTA Update")
                                    val body = item.optString("body", "")
                                    val targetUrl = item.optString("target_url", "/home")
                                    KpstaNotificationHelper.showNotification(
                                        appContext,
                                        id,
                                        title,
                                        body,
                                        targetUrl
                                    )
                                    alertCount++
                                }
                            }
                            KpstaNotificationHelper.saveSeenIds(appContext, incomingIds + seenIds)
                        }
                    }
                }
                conn.disconnect()
            } catch (_: Exception) {
                needsReschedule = true
            } finally {
                jobFinished(params, needsReschedule)
            }
        }
        return true
    }

    override fun onStopJob(params: JobParameters?): Boolean {
        return true
    }
}

