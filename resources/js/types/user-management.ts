/**
 * User Management Types
 * 
 * Type definitions for the user management system including users,
 * roles, permissions, and invitations.
 */

export interface User {
  id: string;
  name: string;
  email: string;
  role_id: string;
  role?: Role;
  is_owner: boolean;
  created_at: string;
  updated_at: string;
}

export interface Role {
  id: string;
  name: string;
  slug: string;
  description: string | null;
  is_system: boolean;
  permissions?: Permission[];
  created_at: string;
  updated_at: string;
}

export interface Permission {
  id: string;
  name: string;
  slug: string;
  description: string | null;
  module: string;
  created_at: string;
  updated_at: string;
}

export interface UserInvitation {
  id: string;
  email: string;
  role_id: string;
  role?: Role;
  token: string;
  invited_by_user_id: string;
  invited_by?: User;
  expires_at: string;
  accepted_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface ImpersonationData {
  admin_id: string;
  admin_name: string;
  admin_email: string;
  tenant_id: string;
  tenant_name: string;
  user_id: string;
  user_name: string;
  user_email: string;
}

export interface ImpersonationLog {
  id: string;
  admin_id: string;
  admin_name: string;
  admin_email: string;
  tenant_id: string;
  tenant_name: string;
  user_id: string;
  user_name: string;
  user_email: string;
  started_at: string;
  ended_at: string | null;
  ip_address: string;
  user_agent: string;
}

// Grouped permissions by module for UI display
export interface PermissionsByModule {
  [module: string]: Permission[];
}

// Form data types
export interface InviteUserForm {
  email: string;
  role_id: string;
}

export interface UpdateUserForm {
  name: string;
  email: string;
}

export interface ChangeRoleForm {
  role_id: string;
}

export interface CreateRoleForm {
  name: string;
  description: string;
}

export interface UpdateRoleForm {
  name: string;
  description: string;
}

export interface AcceptInvitationForm {
  name: string;
  password: string;
  password_confirmation: string;
}
