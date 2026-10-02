import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/widgets/app_top_bar.dart';
import 'auth_providers.dart';

/// WhatsApp OTP Login Screen for Administrators and Membership Staff
class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _phoneController = TextEditingController(text: '919876543210');
  final _otpController = TextEditingController(text: '123456');
  final _formKey = GlobalKey<FormState>();

  @override
  void dispose() {
    _phoneController.dispose();
    _otpController.dispose();
    super.dispose();
  }

  Future<void> _handleSendOtp() async {
    if (!_formKey.currentState!.validate()) return;
    final phone = _phoneController.text.trim();
    final success =
        await ref.read(authNotifierProvider.notifier).requestOtp(phone);
    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('OTP sent via WhatsApp to $phone'),
          backgroundColor: AppColors.success,
        ),
      );
    }
  }

  Future<void> _handleVerifyOtp() async {
    final otp = _otpController.text.trim();
    if (otp.length < 4) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please enter a valid OTP code'),
          backgroundColor: AppColors.error,
        ),
      );
      return;
    }

    final success =
        await ref.read(authNotifierProvider.notifier).verifyOtp(otp);
    if (success && mounted) {
      final user = ref.read(authNotifierProvider).user;
      if (user != null && user.isAdmin) {
        context.go('/admin/dashboard');
      } else if (user != null && user.isMembership) {
        context.go('/membership/dashboard');
      } else {
        context.go('/home');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authNotifierProvider);
    final isLoading = authState.status == AuthStatus.loading;

    return Scaffold(
      appBar: const AppTopBar(
        title: 'Sign In with WhatsApp',
        showBackButton: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const SizedBox(height: 20),
              // WhatsApp Branding / Lock Icon
              Center(
                child: Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: AppColors.green.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.chat_bubble_outline_rounded,
                    size: 60,
                    color: AppColors.green,
                  ),
                ),
              ),
              const SizedBox(height: 20),
              const Text(
                'KPSTA Portal Authentication',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textDark,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                authState.otpSent
                    ? 'Enter the 6-digit verification code sent to your WhatsApp number ${authState.pendingPhone}'
                    : 'Enter your registered WhatsApp mobile number to receive a secure one-time verification code.',
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontSize: 14,
                  color: AppColors.textMuted,
                  height: 1.4,
                ),
              ),
              const SizedBox(height: 32),

              // Error display
              if (authState.errorMessage != null) ...[
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppColors.error.withOpacity(0.08),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(
                      color: AppColors.error.withOpacity(0.3),
                    ),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.error_outline,
                          color: AppColors.error, size: 20),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          authState.errorMessage!,
                          style: const TextStyle(
                            color: AppColors.error,
                            fontSize: 13,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),
              ],

              // Phone Step
              if (!authState.otpSent) ...[
                const Text(
                  'Mobile / WhatsApp Number',
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textDark,
                  ),
                ),
                const SizedBox(height: 8),
                TextFormField(
                  controller: _phoneController,
                  keyboardType: TextInputType.phone,
                  decoration: const InputDecoration(
                    hintText: 'e.g. 919876543210 or 9876543210',
                    prefixIcon: Icon(Icons.phone_outlined),
                  ),
                  validator: (val) {
                    if (val == null || val.trim().length < 10) {
                      return 'Enter a valid 10-12 digit mobile number';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: isLoading ? null : _handleSendOtp,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.green,
                  ),
                  child: isLoading
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: Colors.white,
                          ),
                        )
                      : const Text('Send WhatsApp OTP'),
                ),
              ] else ...[
                // OTP Step
                const Text(
                  '6-Digit OTP Code',
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textDark,
                  ),
                ),
                const SizedBox(height: 8),
                TextFormField(
                  controller: _otpController,
                  keyboardType: TextInputType.number,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    fontSize: 22,
                    letterSpacing: 8,
                    fontWeight: FontWeight.w700,
                  ),
                  maxLength: 6,
                  decoration: const InputDecoration(
                    hintText: '••••••',
                    counterText: '',
                  ),
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: isLoading ? null : _handleVerifyOtp,
                  child: isLoading
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: Colors.white,
                          ),
                        )
                      : const Text('Verify & Continue'),
                ),
                const SizedBox(height: 12),
                TextButton(
                  onPressed: isLoading
                      ? null
                      : () =>
                          ref.read(authNotifierProvider.notifier).resetOtp(),
                  child: const Text('Change Mobile Number'),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
