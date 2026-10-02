import 'package:flutter/material.dart';
import '../constants/app_colors.dart';

/// Horizontal scrolling category/filter chips
class FilterChipsBar<T> extends StatelessWidget {
  final List<T> items;
  final T? selectedItem;
  final String Function(T item) labelBuilder;
  final ValueChanged<T?> onSelected;
  final String allLabel;

  const FilterChipsBar({
    super.key,
    required this.items,
    required this.selectedItem,
    required this.labelBuilder,
    required this.onSelected,
    this.allLabel = 'All',
  });

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 48,
      child: ListView(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
        children: [
          ChoiceChip(
            label: Text(allLabel),
            selected: selectedItem == null,
            onSelected: (selected) {
              if (selected) onSelected(null);
            },
            selectedColor: AppColors.primary,
            labelStyle: TextStyle(
              color: selectedItem == null ? AppColors.white : AppColors.text,
              fontSize: 13,
              fontWeight:
                  selectedItem == null ? FontWeight.w600 : FontWeight.normal,
            ),
          ),
          const SizedBox(width: 8),
          ...items.map((item) {
            final isSelected = selectedItem == item;
            return Padding(
              padding: const EdgeInsets.only(right: 8),
              child: ChoiceChip(
                label: Text(labelBuilder(item)),
                selected: isSelected,
                onSelected: (selected) {
                  onSelected(selected ? item : null);
                },
                selectedColor: AppColors.primary,
                labelStyle: TextStyle(
                  color: isSelected ? AppColors.white : AppColors.text,
                  fontSize: 13,
                  fontWeight:
                      isSelected ? FontWeight.w600 : FontWeight.normal,
                ),
              ),
            );
          }),
        ],
      ),
    );
  }
}
