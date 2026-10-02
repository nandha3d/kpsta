class UserModel {
  final int id;
  final String username;
  final String? email;
  final String? phone;
  final String role;
  final List<String> roles;
  final List<String> permissions;
  final bool isAdmin;
  final bool isMembership;

  UserModel({
    required this.id,
    required this.username,
    this.email,
    this.phone,
    required this.role,
    required this.roles,
    required this.permissions,
    required this.isAdmin,
    required this.isMembership,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    final rolesList = (json['roles'] as List<dynamic>?)
            ?.map((e) => e.toString())
            .toList() ??
        [];
    final permsList = (json['permissions'] as List<dynamic>?)
            ?.map((e) => e.toString())
            .toList() ??
        [];

    final roleStr = json['role']?.toString() ??
        (rolesList.isNotEmpty ? rolesList.first : 'guest');

    return UserModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      username: json['username']?.toString() ?? '',
      email: json['email']?.toString(),
      phone: json['phone']?.toString(),
      role: roleStr,
      roles: rolesList,
      permissions: permsList,
      isAdmin: json['is_admin'] == true ||
          roleStr.toLowerCase() == 'admin' ||
          rolesList.contains('admin') ||
          rolesList.contains('Admin'),
      isMembership: json['is_membership'] == true ||
          roleStr.toLowerCase().contains('member') ||
          rolesList.any((r) => r.toLowerCase().contains('member')),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'username': username,
        'email': email,
        'phone': phone,
        'role': role,
        'roles': roles,
        'permissions': permissions,
        'is_admin': isAdmin,
        'is_membership': isMembership,
      };
}
