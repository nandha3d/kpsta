import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../auth/presentation/auth_providers.dart';

class DonationScreen extends ConsumerStatefulWidget {
  const DonationScreen({super.key});

  @override
  ConsumerState<DonationScreen> createState() => _DonationScreenState();
}

class _DonationScreenState extends ConsumerState<DonationScreen> {
  final _formKey = GlobalKey<FormState>();
  final _amountController = TextEditingController(text: '500');
  final _nameController = TextEditingController();
  final _mobileController = TextEditingController();
  final _emailController = TextEditingController();
  final _purposeController = TextEditingController(text: 'Teachers Welfare Fund');
  bool _isProcessing = false;
  String? _paymentReference;

  @override
  void dispose() {
    _amountController.dispose();
    _nameController.dispose();
    _mobileController.dispose();
    _emailController.dispose();
    _purposeController.dispose();
    super.dispose();
  }

  Future<void> _handleInitiateDonation() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isProcessing = true);

    try {
      final client = ref.read(apiClientProvider);
      final response = await client.post(
        ApiConstants.donationInitiate,
        data: {
          'amount': double.tryParse(_amountController.text.trim()) ?? 500,
          'name': _nameController.text.trim(),
          'mobile': _mobileController.text.trim(),
          'email': _emailController.text.trim(),
          'purpose': _purposeController.text.trim(),
        },
      );

      final data = response['data'] as Map<String, dynamic>;
      final orderRef = data['token']?.toString() ??
          data['reference_id']?.toString() ??
          data['order_id']?.toString() ??
          '';

      setState(() {
        _paymentReference = orderRef;
      });
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
      if (mounted) setState(() => _isProcessing = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: const AppTopBar(title: 'Support KPSTA / Donate'),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: _paymentReference != null
            ? _buildSuccessReceipt()
            : Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: AppColors.green.withOpacity(0.08),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: AppColors.green.withOpacity(0.2),
                        ),
                      ),
                      child: const Row(
                        children: [
                          Icon(
                            Icons.volunteer_activism,
                            color: AppColors.green,
                            size: 32,
                          ),
                          SizedBox(width: 14),
                          Expanded(
                            child: Text(
                              'Contributions directly support teacher welfare programs, legal aid, and public education defense.',
                              style: TextStyle(
                                fontSize: 13,
                                color: AppColors.textDark,
                                height: 1.4,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 24),

                    // Quick Amounts
                    const Text(
                      'Select Contribution Amount (₹)',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textDark,
                      ),
                    ),
                    const SizedBox(height: 10),
                    Row(
                      children: ['500', '1000', '2500', '5000'].map((amt) {
                        final isSelected = _amountController.text == amt;
                        return Expanded(
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 4),
                            child: OutlinedButton(
                              style: OutlinedButton.styleFrom(
                                backgroundColor: isSelected
                                    ? AppColors.primary
                                    : AppColors.white,
                                foregroundColor: isSelected
                                    ? AppColors.white
                                    : AppColors.primary,
                                padding:
                                    const EdgeInsets.symmetric(vertical: 10),
                              ),
                              onPressed: () {
                                setState(() {
                                  _amountController.text = amt;
                                });
                              },
                              child: Text('₹$amt'),
                            ),
                          ),
                        );
                      }).toList(),
                    ),
                    const SizedBox(height: 14),

                    TextFormField(
                      controller: _amountController,
                      keyboardType: TextInputType.number,
                      decoration: const InputDecoration(
                        labelText: 'Custom Amount (₹) *',
                        prefixIcon: Icon(Icons.currency_rupee),
                      ),
                      validator: (val) {
                        final parsed = double.tryParse(val ?? '');
                        if (parsed == null || parsed <= 0) {
                          return 'Enter a valid amount';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 14),

                    TextFormField(
                      controller: _nameController,
                      decoration: const InputDecoration(
                        labelText: 'Full Name *',
                        prefixIcon: Icon(Icons.person_outline),
                      ),
                      validator: (val) =>
                          val == null || val.isEmpty ? 'Name is required' : null,
                    ),
                    const SizedBox(height: 14),

                    TextFormField(
                      controller: _mobileController,
                      keyboardType: TextInputType.phone,
                      decoration: const InputDecoration(
                        labelText: 'Mobile Number *',
                        prefixIcon: Icon(Icons.phone_outlined),
                      ),
                      validator: (val) => val == null || val.length < 10
                          ? 'Valid 10-digit mobile required'
                          : null,
                    ),
                    const SizedBox(height: 14),

                    TextFormField(
                      controller: _emailController,
                      keyboardType: TextInputType.emailAddress,
                      decoration: const InputDecoration(
                        labelText: 'Email Address (optional)',
                        prefixIcon: Icon(Icons.email_outlined),
                      ),
                    ),
                    const SizedBox(height: 14),

                    TextFormField(
                      controller: _purposeController,
                      decoration: const InputDecoration(
                        labelText: 'Purpose / Remarks',
                        prefixIcon: Icon(Icons.comment_outlined),
                      ),
                    ),
                    const SizedBox(height: 24),

                    ElevatedButton(
                      onPressed:
                          _isProcessing ? null : _handleInitiateDonation,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.green,
                        minimumSize: const Size(double.infinity, 48),
                      ),
                      child: _isProcessing
                          ? const SizedBox(
                              height: 20,
                              width: 20,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: Colors.white,
                              ),
                            )
                          : Text('Proceed to Pay ₹${_amountController.text}'),
                    ),
                  ],
                ),
              ),
      ),
    );
  }

  Widget _buildSuccessReceipt() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        const SizedBox(height: 20),
        const Icon(
          Icons.check_circle_rounded,
          color: AppColors.green,
          size: 70,
        ),
        const SizedBox(height: 16),
        const Text(
          'Donation Order Created!',
          textAlign: TextAlign.center,
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.w700,
            color: AppColors.textDark,
          ),
        ),
        const SizedBox(height: 8),
        const Text(
          'Thank you for standing in solidarity with KPSTA. Your reference order has been recorded.',
          textAlign: TextAlign.center,
          style: TextStyle(fontSize: 14, color: AppColors.textMuted),
        ),
        const SizedBox(height: 24),
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: AppColors.bgLight,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('Reference ID:',
                      style: TextStyle(color: AppColors.textMuted)),
                  Text(
                    _paymentReference!,
                    style: const TextStyle(
                      fontWeight: FontWeight.w700,
                      color: AppColors.primary,
                    ),
                  ),
                ],
              ),
              const Divider(height: 20),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('Amount:',
                      style: TextStyle(color: AppColors.textMuted)),
                  Text(
                    '₹${_amountController.text}',
                    style: const TextStyle(
                      fontWeight: FontWeight.w700,
                      fontSize: 16,
                      color: AppColors.textDark,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
        const SizedBox(height: 24),
        OutlinedButton(
          onPressed: () {
            setState(() {
              _paymentReference = null;
            });
          },
          child: const Text('Make Another Contribution'),
        ),
      ],
    );
  }
}
