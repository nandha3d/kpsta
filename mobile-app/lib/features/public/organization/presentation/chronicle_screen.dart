import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/app_colors.dart';
import '../../../../core/widgets/app_top_bar.dart';
import '../../../../core/widgets/bearer_card.dart';
import '../../../../core/widgets/kpsta_footer.dart';
import 'former_leaders_screen.dart';

/// Dedicated Chronicle of KPSTA page showcasing the association's heritage,
/// organizational structure, publications, affiliations, and former leaders archive.
class ChronicleScreen extends ConsumerWidget {
  const ChronicleScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final leadersAsync = ref.watch(formerLeadersProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      appBar: const AppTopBar(title: 'Chronicle of KPSTA'),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 28),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Hero Heritage Card
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF0A5863), Color(0xFF016D77), Color(0xFF0D7C66)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(22),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF016D77).withValues(alpha: 0.24),
                    blurRadius: 16,
                    offset: const Offset(0, 6),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      ClipOval(
                        child: Container(
                          color: Colors.white,
                          padding: const EdgeInsets.all(4),
                          child: Image.asset(
                            'assets/images/logo.png',
                            width: 48,
                            height: 48,
                            fit: BoxFit.cover,
                            errorBuilder: (_, _, _) => const Icon(
                              Icons.history_edu_rounded,
                              size: 40,
                              color: AppColors.primary,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 14),
                      const Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'CHRONICLE OF KPSTA',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.w900,
                                color: Colors.white,
                                letterSpacing: 0.4,
                              ),
                            ),
                            SizedBox(height: 2),
                            Text(
                              'കെ.പി.എസ്.ടി.എ. ചരിത്ര നാൾവഴികൾ',
                              style: TextStyle(
                                fontSize: 12.5,
                                fontWeight: FontWeight.w600,
                                color: Color(0xFFD6F1F3),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.14),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: Colors.white.withValues(alpha: 0.22),
                      ),
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.format_quote_rounded, color: Color(0xFFFFD5B8), size: 20),
                        SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            'Unite for Quality Education · Better education for a better world',
                            style: TextStyle(
                              fontSize: 12.5,
                              fontWeight: FontWeight.w700,
                              color: Colors.white,
                              height: 1.35,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 14),
                  const Text(
                    'Kerala Pradesh School Teachers\' Association (KPSTA) stands as a steadfast voice for school teachers and public education across Kerala — championing teacher welfare, academic excellence, and democratic values across schools, sub-districts, education districts, and all 14 revenue districts.',
                    style: TextStyle(
                      fontSize: 13.5,
                      color: Color(0xFFEAF7F8),
                      height: 1.55,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 18),

            // Organizational Pillars & Institutional Facts
            const Text(
              'Institutional Pillars',
              style: TextStyle(
                fontSize: 17,
                fontWeight: FontWeight.w800,
                color: Color(0xFF0F1E24),
              ),
            ),
            const SizedBox(height: 10),

            _ChroniclePillarCard(
              icon: Icons.public_rounded,
              accentColor: const Color(0xFFEE6A2A),
              title: 'National & International Affiliations',
              subtitle: 'ദേശീയ - അന്തർദേശീയ അഫിലിയേഷൻ',
              description:
                  'Affiliated to the All India Primary Teachers\' Federation (AIPTF), All India Federation of Teachers\' Organisations (AIFTO), and Education International (EI), connecting Kerala\'s classrooms to global educational solidarity.',
            ),
            const SizedBox(height: 10),
            _ChroniclePillarCard(
              icon: Icons.account_balance_rounded,
              accentColor: const Color(0xFF016D77),
              title: 'State Headquarters — KPSTA Bhavan',
              subtitle: 'സംസ്ഥാന ആസ്ഥാനം · തിരുവനന്തപുരം',
              description:
                  'Anchored at KPSTA Bhavan, Chinmaya School Lane, Kunnumpuram, Thiruvananthapuram - 1, coordinating organizational policy, legal representation, memorandums, and teacher support initiatives.',
            ),
            const SizedBox(height: 10),
            _ChroniclePillarCard(
              icon: Icons.menu_book_rounded,
              accentColor: const Color(0xFF2E9E14),
              title: 'Adyapaka Sabdham (അധ്യാപക ശബ്ദം)',
              subtitle: 'Official Organizational Publication',
              description:
                  'KPSTA\'s official publication chronicling policy debates, service matters, academic discourse, and organizational milestones for educators across Kerala.',
              actionLabel: 'Open Membership & Adyapaka Sabdham →',
              onTap: () => context.push('/services/adayapaka'),
            ),
            const SizedBox(height: 10),
            _ChroniclePillarCard(
              icon: Icons.account_tree_rounded,
              accentColor: const Color(0xFF6B21A8),
              title: 'Four-Tier Democratic Structure',
              subtitle: 'സംഘടനാ സംവിധാനം',
              description:
                  'Built from the grassroots through School & Branch Units, Sub-District & Education District Committees, 14 Revenue District Committees, and the elected State Committee.',
              actionLabel: 'Explore Organization & Districts →',
              onTap: () => context.go('/organization'),
            ),

            const SizedBox(height: 20),

            // Past State Leadership / Former Leaders Chronicle Section
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Torchbearers of the Movement',
                        style: TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF0F1E24),
                        ),
                      ),
                      Text(
                        'മുൻകാല സംസ്ഥാന ഭാരവാഹികൾ · Former Leaders Archive',
                        style: TextStyle(
                          fontSize: 11.5,
                          color: Color(0xFF64748B),
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),
                TextButton(
                  onPressed: () => context.push('/organization/former-leaders'),
                  child: const Text(
                    'Full Archive →',
                    style: TextStyle(
                      fontSize: 12.5,
                      fontWeight: FontWeight.w800,
                      color: AppColors.primary,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),

            leadersAsync.when(
              loading: () => const Center(
                child: Padding(
                  padding: EdgeInsets.symmetric(vertical: 20),
                  child: CircularProgressIndicator(color: AppColors.primary),
                ),
              ),
              error: (_, _) => const SizedBox.shrink(),
              data: (leaders) {
                if (leaders.isEmpty) {
                  return Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.9),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: const Text(
                      'Visit the Former Leaders Archive inside Organization for historical leadership records.',
                      style: TextStyle(fontSize: 13, color: Color(0xFF475569)),
                    ),
                  );
                }

                return Column(
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: leaders.take(3).map((leader) {
                        final item = leader as Map<String, dynamic>;
                        final positions =
                            item['all_positions'] as List<dynamic>? ?? [];
                        final firstPos = positions.isNotEmpty
                            ? positions.first as Map<String, dynamic>
                            : null;
                        final designation =
                            firstPos?['designation']?.toString() ??
                                item['designation']?.toString() ??
                                '';
                        final year = firstPos?['year']?.toString() ?? '';
                        final roleText = year.isNotEmpty
                            ? '$designation ($year)'
                            : designation;

                        return Expanded(
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 3),
                            child: BearerCard(
                              name: item['name']?.toString() ?? '',
                              designation: roleText,
                              photoUrl: item['photo_url']?.toString(),
                              phone: item['phone']?.toString(),
                              aspectRatio: 1.0,
                            ),
                          ),
                        );
                      }).toList(),
                    ),
                    const SizedBox(height: 12),
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton.icon(
                        icon: const Icon(Icons.history_edu_rounded, size: 18),
                        label: const Text(
                          'Browse All Former Leaders & Past Terms →',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: AppColors.primary,
                          backgroundColor: Colors.white.withValues(alpha: 0.92),
                          side: const BorderSide(
                            color: AppColors.primary,
                            width: 1.2,
                          ),
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                        onPressed: () =>
                            context.push('/organization/former-leaders'),
                      ),
                    ),
                  ],
                );
              },
            ),

            const SizedBox(height: 20),

            // Explore Visual History
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: 0.9),
                borderRadius: BorderRadius.circular(18),
                border: Border.all(color: Colors.white),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.04),
                    blurRadius: 10,
                    offset: const Offset(0, 3),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Explore KPSTA Visual Archives',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF0F1E24),
                    ),
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'Relive KPSTA state conferences, campaigns, and educational events through official galleries and reaction posters.',
                    style: TextStyle(
                      fontSize: 12.5,
                      color: Color(0xFF475569),
                      height: 1.4,
                    ),
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Expanded(
                        child: ElevatedButton.icon(
                          icon: const Icon(Icons.photo_library_outlined, size: 16),
                          label: const Text(
                            'Photo Gallery',
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF016D77),
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                          onPressed: () => context.push('/gallery'),
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: ElevatedButton.icon(
                          icon: const Icon(Icons.campaign_outlined, size: 16),
                          label: const Text(
                            'Reaction Posters',
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFFEE6A2A),
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                          onPressed: () => context.push('/reaction-gallery'),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            const SizedBox(height: 24),
            const KpstaFooter(),
          ],
        ),
      ),
    );
  }
}

class _ChroniclePillarCard extends StatelessWidget {
  final IconData icon;
  final Color accentColor;
  final String title;
  final String subtitle;
  final String description;
  final String? actionLabel;
  final VoidCallback? onTap;

  const _ChroniclePillarCard({
    required this.icon,
    required this.accentColor,
    required this.title,
    required this.subtitle,
    required this.description,
    this.actionLabel,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.92),
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: Colors.white, width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(9),
                decoration: BoxDecoration(
                  color: accentColor.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: accentColor, size: 22),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 14.5,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF0F1E24),
                      ),
                    ),
                    Text(
                      subtitle,
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        color: accentColor,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Text(
            description,
            style: const TextStyle(
              fontSize: 12.8,
              color: Color(0xFF475569),
              height: 1.5,
            ),
          ),
          if (actionLabel != null && onTap != null) ...[
            const SizedBox(height: 10),
            InkWell(
              onTap: onTap,
              borderRadius: BorderRadius.circular(8),
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 4),
                child: Text(
                  actionLabel!,
                  style: TextStyle(
                    fontSize: 12.5,
                    fontWeight: FontWeight.w800,
                    color: accentColor,
                  ),
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}

