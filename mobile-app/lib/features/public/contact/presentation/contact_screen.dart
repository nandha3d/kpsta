import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/error_state.dart';
import '../../../auth/presentation/auth_providers.dart';

final contactInfoProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final client = ref.watch(apiClientProvider);
  final response = await client.get(ApiConstants.contact);
  return response['data'] as Map<String, dynamic>;
});

class ContactScreen extends ConsumerStatefulWidget {
  const ContactScreen({super.key});

  @override
  ConsumerState<ContactScreen> createState() => _ContactScreenState();
}

class _ContactScreenState extends ConsumerState<ContactScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _subjectController = TextEditingController();
  final _messageController = TextEditingController();
  bool _isSubmitting = false;

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _subjectController.dispose();
    _messageController.dispose();
    super.dispose();
  }

  Future<void> _submitEnquiry() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isSubmitting = true);

    try {
      final client = ref.read(apiClientProvider);
      await client.post(
        ApiConstants.contactEnquiry,
        data: {
          'name': _nameController.text.trim(),
          'email': _emailController.text.trim(),
          'phone': _phoneController.text.trim(),
          'subject': _subjectController.text.trim(),
          'message': _messageController.text.trim(),
        },
      );

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Your message has been sent successfully!'),
            backgroundColor: AppColors.success,
          ),
        );
        _formKey.currentState!.reset();
        _nameController.clear();
        _emailController.clear();
        _phoneController.clear();
        _subjectController.clear();
        _messageController.clear();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              e.toString().replaceAll('ApiException: ', ''),
            ),
            backgroundColor: AppColors.error,
          ),
        );
      }
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

  Future<void> _makeCall(String phone) async {
    final uri = Uri.parse('tel:$phone');
    if (await canLaunchUrl(uri)) await launchUrl(uri);
  }

  Future<void> _sendMail(String email) async {
    final uri = Uri.parse('mailto:$email');
    if (await canLaunchUrl(uri)) await launchUrl(uri);
  }

  @override
  Widget build(BuildContext context) {
    final contactAsync = ref.watch(contactInfoProvider);

    return Scaffold(
      appBar: const AppTopBar(title: 'Contact KPSTA'),
      body: contactAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
        error: (err, stack) => ErrorState(
          message: err.toString().replaceAll('ApiException: ', ''),
          onRetry: () => ref.refresh(contactInfoProvider),
        ),
        data: (contact) {
          final address = contact['address']?.toString() ??
              'KPSTA BHAVAN, Chinmaya School Lane, Kunnumpuram, Trivandrum -1';
          final phone = contact['phone']?.toString() ?? '0471 - 2575797';
          final email = contact['email']?.toString() ?? 'kpsta.in@gmail.com';

          return SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Association Info Card
                Container(
                  padding: const EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    color: AppColors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'State Headquarters',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                          color: AppColors.primaryDark,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Icon(Icons.location_on_outlined,
                              color: AppColors.primary, size: 20),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Text(
                              address,
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppColors.text,
                                height: 1.4,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      InkWell(
                        onTap: () => _makeCall(phone),
                        child: Row(
                          children: [
                            const Icon(Icons.phone_outlined,
                                color: AppColors.green, size: 20),
                            const SizedBox(width: 10),
                            Text(
                              phone,
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppColors.green,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 12),
                      InkWell(
                        onTap: () => _sendMail(email),
                        child: Row(
                          children: [
                            const Icon(Icons.email_outlined,
                                color: AppColors.indigo, size: 20),
                            const SizedBox(width: 10),
                            Text(
                              email,
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppColors.indigo,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 28),

                // Enquiry Form
                const Text(
                  'Send Us a Message',
                  style: TextStyle(
                    fontSize: 17,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textDark,
                  ),
                ),
                const SizedBox(height: 6),
                const Text(
                  'Have an inquiry or feedback? Complete the form below and state officials will get in touch.',
                  style: TextStyle(fontSize: 13, color: AppColors.textMuted),
                ),
                const SizedBox(height: 18),

                Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      TextFormField(
                        controller: _nameController,
                        decoration:
                            const InputDecoration(hintText: 'Full Name *'),
                        validator: (v) =>
                            v == null || v.isEmpty ? 'Name is required' : null,
                      ),
                      const SizedBox(height: 12),
                      TextFormField(
                        controller: _phoneController,
                        keyboardType: TextInputType.phone,
                        decoration: const InputDecoration(
                            hintText: 'Mobile / Contact No *'),
                        validator: (v) => v == null || v.length < 10
                            ? 'Valid mobile number required'
                            : null,
                      ),
                      const SizedBox(height: 12),
                      TextFormField(
                        controller: _emailController,
                        keyboardType: TextInputType.emailAddress,
                        decoration:
                            const InputDecoration(hintText: 'Email Address'),
                      ),
                      const SizedBox(height: 12),
                      TextFormField(
                        controller: _subjectController,
                        decoration: const InputDecoration(
                            hintText: 'Subject / Topic *'),
                        validator: (v) => v == null || v.isEmpty
                            ? 'Subject is required'
                            : null,
                      ),
                      const SizedBox(height: 12),
                      TextFormField(
                        controller: _messageController,
                        maxLines: 4,
                        decoration:
                            const InputDecoration(hintText: 'Your Message *'),
                        validator: (v) => v == null || v.isEmpty
                            ? 'Message is required'
                            : null,
                      ),
                      const SizedBox(height: 20),
                      ElevatedButton(
                        onPressed: _isSubmitting ? null : _submitEnquiry,
                        child: _isSubmitting
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  color: Colors.white,
                                ),
                              )
                            : const Text('Submit Message'),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
