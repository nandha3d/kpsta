import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';

/// Modern Expandable Services & Portals Widget
/// Collapsed by default; smoothly expands into a 10-item comprehensive services grid when tapped.
class QuickMenuWidget extends StatefulWidget {
  final bool isAuthenticated;

  const QuickMenuWidget({
    super.key,
    required this.isAuthenticated,
  });

  @override
  State<QuickMenuWidget> createState() => _QuickMenuWidgetState();
}

class _QuickMenuWidgetState extends State<QuickMenuWidget>
    with SingleTickerProviderStateMixin {
  bool _isExpanded = false;
  late final AnimationController _rotationController;
  late final Animation<double> _rotationAnimation;

  @override
  void initState() {
    super.initState();
    _rotationController = AnimationController(
      duration: const Duration(milliseconds: 280),
      vsync: this,
    );
    _rotationAnimation = Tween<double>(begin: 0.0, end: 0.5).animate(
      CurvedAnimation(
        parent: _rotationController,
        curve: Curves.easeInOutCubic,
      ),
    );
  }

  @override
  void dispose() {
    _rotationController.dispose();
    super.dispose();
  }

  void _toggleExpand() {
    HapticFeedback.lightImpact();
    setState(() {
      _isExpanded = !_isExpanded;
      if (_isExpanded) {
        _rotationController.forward();
      } else {
        _rotationController.reverse();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 14, sigmaY: 14),
          child: Container(
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.75),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: Colors.white.withValues(alpha: 0.90),
                width: 1.5,
              ),
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFF016D77).withValues(alpha: 0.08),
                  blurRadius: 16,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                // Header Trigger Row
                Material(
                  color: Colors.transparent,
                  child: InkWell(
                    onTap: _toggleExpand,
                    borderRadius: BorderRadius.circular(16),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                      child: Row(
                        children: [
                          // Badge Icon
                          Container(
                            width: 44,
                            height: 44,
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                                colors: [
                                  Color(0xFF016D77),
                                  Color(0xFF0F1E24),
                                ],
                              ),
                              borderRadius: BorderRadius.circular(12),
                              boxShadow: [
                                BoxShadow(
                                  color: const Color(0xFF016D77).withValues(alpha: 0.25),
                                  blurRadius: 6,
                                  offset: const Offset(0, 2),
                                ),
                              ],
                            ),
                            child: const Center(
                              child: Icon(
                                Icons.grid_view_rounded,
                                color: Colors.white,
                                size: 22,
                              ),
                            ),
                          ),
                          const SizedBox(width: 14),

                          // Title & Description
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Website Directory',
                                  style: TextStyle(
                                    fontSize: 14.5,
                                    fontWeight: FontWeight.w800,
                                    color: Color(0xFF0F1E24),
                                    letterSpacing: 0.2,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                const SizedBox(height: 2),
                                const Text(
                                  'വെബ്സൈറ്റ് ഡയറക്ടറി · All Services',
                                  style: TextStyle(
                                    fontSize: 11.5,
                                    color: Color(0xFF64748B),
                                    fontWeight: FontWeight.w500,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                          ),

                          // Expand / Collapse Animated Indicator
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                            decoration: BoxDecoration(
                              color: _isExpanded
                                  ? const Color(0xFF016D77).withValues(alpha: 0.1)
                                  : const Color(0xFFE5F3F4),
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text(
                                  _isExpanded ? 'Hide' : 'Expand',
                                  style: const TextStyle(
                                    fontSize: 11.5,
                                    fontWeight: FontWeight.w700,
                                    color: Color(0xFF016D77),
                                  ),
                                ),
                                const SizedBox(width: 2),
                                RotationTransition(
                                  turns: _rotationAnimation,
                                  child: const Icon(
                                    Icons.keyboard_arrow_down,
                                    color: Color(0xFF016D77),
                                    size: 18,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),

                // Expandable Body: Exactly 10 comprehensive service modules
                AnimatedCrossFade(
                  firstChild: const SizedBox(width: double.infinity, height: 0),
                  secondChild: Container(
                    padding: const EdgeInsets.fromLTRB(14, 4, 14, 14),
                    child: Column(
                      children: [
                        const Divider(height: 1, color: Color(0xFFE2E8F0)),
                        const SizedBox(height: 12),

                        // Row 1: 1. Membership & Adyapaka Sabdham, 2. Representation (Memorandum)
                        IntrinsicHeight(
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.card_membership_rounded,
                                  title: 'Membership & Adyapaka Sabdham',
                                  malayalam: 'അംഗത്വം & അധ്യാപക ശബ്ദം',
                                  badgeColor: const Color(0xFFEE6A2A),
                                  bgColor: const Color(0xFFFFF6F2),
                                  borderColor: const Color(0xFFEE6A2A).withValues(alpha: 0.25),
                                  onTap: () => context.push('/services/adayapaka'),
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.assignment_outlined,
                                  title: 'Representation (Memorandum)',
                                  malayalam: 'നിവേദനങ്ങൾ / മെമ്മോറാണ്ടം',
                                  badgeColor: const Color(0xFF016D77),
                                  bgColor: const Color(0xFFF0F9FA),
                                  borderColor: const Color(0xFF016D77).withValues(alpha: 0.25),
                                  onTap: () => context.go('/organization?tab=memorandums'),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 10),

                        // Row 2: 3. Notice & Posters, 4. Forms
                        IntrinsicHeight(
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.campaign_rounded,
                                  title: 'Notice & Posters',
                                  malayalam: 'നോട്ടീസുകൾ & പോസ്റ്ററുകൾ',
                                  badgeColor: const Color(0xFFD97706),
                                  bgColor: const Color(0xFFFEF9E7),
                                  borderColor: const Color(0xFFD97706).withValues(alpha: 0.25),
                                  onTap: () => context.go('/organization?tab=notice_poster'),
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.file_copy_outlined,
                                  title: 'Forms',
                                  malayalam: 'ഫോമുകൾ',
                                  badgeColor: const Color(0xFF2563EB),
                                  bgColor: const Color(0xFFEFF6FF),
                                  borderColor: const Color(0xFF2563EB).withValues(alpha: 0.25),
                                  onTap: () => context.go('/downloads?tab=forms'),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 10),

                        // Row 3: 5. Software Tools, 6. Melakal
                        IntrinsicHeight(
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.terminal_rounded,
                                  title: 'Software Tools',
                                  malayalam: 'സോഫ്റ്റ്‌വെയർ ടൂളുകൾ',
                                  badgeColor: const Color(0xFF7C3AED),
                                  bgColor: const Color(0xFFF5F3FF),
                                  borderColor: const Color(0xFF7C3AED).withValues(alpha: 0.25),
                                  onTap: () => context.go('/downloads?tab=softwares'),
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.emoji_events_outlined,
                                  title: 'Melakal',
                                  malayalam: 'മേളകൾ',
                                  badgeColor: const Color(0xFFE11D48),
                                  bgColor: const Color(0xFFFFF1F2),
                                  borderColor: const Color(0xFFE11D48).withValues(alpha: 0.25),
                                  onTap: () => context.go('/downloads?tab=melakal'),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 10),

                        // Row 4: 7. Service Corner, 8. Academic Corner
                        IntrinsicHeight(
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.volunteer_activism_rounded,
                                  title: 'Service Corner',
                                  malayalam: 'സർവീസ് കോർണർ',
                                  badgeColor: const Color(0xFF059669),
                                  bgColor: const Color(0xFFECFDF5),
                                  borderColor: const Color(0xFF059669).withValues(alpha: 0.25),
                                  onTap: () => context.push('/services'),
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.school_rounded,
                                  title: 'Academic Corner',
                                  malayalam: 'അക്കാദമിക് കോർണർ',
                                  badgeColor: const Color(0xFF4F46E5),
                                  bgColor: const Color(0xFFEEF2FF),
                                  borderColor: const Color(0xFF4F46E5).withValues(alpha: 0.25),
                                  onTap: () => context.go('/downloads?tab=academic_corner'),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 10),

                        // Row 5: 9. Online Links, 10. Contact Us
                        IntrinsicHeight(
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.link_rounded,
                                  title: 'Online Links',
                                  malayalam: 'ഓൺലൈൻ ലിങ്കുകൾ',
                                  badgeColor: const Color(0xFF0891B2),
                                  bgColor: const Color(0xFFECFEFF),
                                  borderColor: const Color(0xFF0891B2).withValues(alpha: 0.25),
                                  onTap: () => context.push('/links/quick'),
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: _buildServiceItem(
                                  context: context,
                                  icon: Icons.phone_in_talk_rounded,
                                  title: 'Contact Us',
                                  malayalam: 'ബന്ധപ്പെടുക',
                                  badgeColor: const Color(0xFF16A34A),
                                  bgColor: const Color(0xFFF0FDF4),
                                  borderColor: const Color(0xFF16A34A).withValues(alpha: 0.25),
                                  onTap: () => context.push('/contact'),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  crossFadeState: _isExpanded
                      ? CrossFadeState.showSecond
                      : CrossFadeState.showFirst,
                  duration: const Duration(milliseconds: 280),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildServiceItem({
    required BuildContext context,
    required IconData icon,
    required String title,
    required String malayalam,
    required Color badgeColor,
    required Color bgColor,
    required Color borderColor,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: () {
          HapticFeedback.lightImpact();
          onTap();
        },
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
          decoration: BoxDecoration(
            color: bgColor,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: borderColor, width: 1),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Container(
                    width: 32,
                    height: 32,
                    decoration: BoxDecoration(
                      color: badgeColor,
                      borderRadius: BorderRadius.circular(8),
                      boxShadow: [
                        BoxShadow(
                          color: badgeColor.withValues(alpha: 0.3),
                          blurRadius: 4,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Center(
                      child: Icon(icon, color: Colors.white, size: 17),
                    ),
                  ),
                  Icon(
                    Icons.arrow_forward_rounded,
                    size: 15,
                    color: badgeColor.withValues(alpha: 0.7),
                  ),
                ],
              ),
              const SizedBox(height: 7),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 11.5,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF0F1E24),
                  height: 1.15,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
              const SizedBox(height: 2),
              Text(
                malayalam,
                style: const TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w500,
                  color: Color(0xFF64748B),
                  height: 1.15,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
