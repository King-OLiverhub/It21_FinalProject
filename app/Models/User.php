<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Role Constants
    const ROLE_ADMIN = 'admin';
    const ROLE_SECURITY_ANALYST = 'security_analyst';
    const ROLE_SYSTEM_ADMIN = 'system_admin';
    const ROLE_NETWORK_ADMIN = 'network_admin';
    const ROLE_IT_SECURITY_STAFF = 'it_security_staff';
    const ROLE_INFO_SECURITY_OFFICER = 'info_security_officer';

    /**
     * Get all roles with their descriptions
     */
    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN => [
                'name' => 'Administrator',
                'description' => 'Manage user accounts and assign roles and permissions. Configure system settings and threat intelligence sources. View all alerts, logs, and reports. Maintain overall system security and data integrity.',
                'permissions' => [
                    'manage_users',
                    'configure_system',
                    'view_all_alerts',
                    'view_all_logs',
                    'view_all_reports',
                    'maintain_security',
                    'manage_roles',
                    'delete_data',
                ]
            ],
            self::ROLE_SECURITY_ANALYST => [
                'name' => 'Security Analyst',
                'description' => 'Monitor real-time network activity. Register new servers under the monitoring profile. Analyze security alerts and threat patterns. Investigate suspicious IPs, domains, and activities. Generate security reports and insights from dashboard data.',
                'permissions' => [
                    'view_dashboard',
                    'view_threats',
                    'view_alerts',
                    'analyze_threats',
                    'investigate_ips',
                    'generate_reports',
                    'view_analytics',
                    'add_servers',
                ]
            ],
            self::ROLE_SYSTEM_ADMIN => [
                'name' => 'System Administrator',
                'description' => 'Maintain system performance and server availability. Manage database operations and backups. Ensure system stability and uptime. Handle technical issues and system maintenance tasks.',
                'permissions' => [
                    'view_dashboard',
                    'manage_system',
                    'manage_database',
                    'manage_backups',
                    'view_system_logs',
                    'perform_maintenance',
                    'monitor_performance',
                ]
            ],
            self::ROLE_NETWORK_ADMIN => [
                'name' => 'Network Administrator',
                'description' => 'Monitor network-related threat activities. Analyze suspicious network traffic and IP behavior. Support identification of network-based security incidents. Review network security logs and reports.',
                'permissions' => [
                    'view_dashboard',
                    'view_threats',
                    'view_network_logs',
                    'analyze_traffic',
                    'monitor_network',
                    'view_ip_reputation',
                    'network_incident_response',
                ]
            ],
            self::ROLE_IT_SECURITY_STAFF => [
                'name' => 'IT Security Staff',
                'description' => 'Respond to security alerts and notifications. Assist in incident response and threat mitigation. Monitor assigned security events in the dashboard. Support implementation of security policies and controls.',
                'permissions' => [
                    'view_dashboard',
                    'view_alerts',
                    'respond_to_alerts',
                    'incident_response',
                    'view_assigned_events',
                    'implement_policies',
                ]
            ],
            self::ROLE_INFO_SECURITY_OFFICER => [
                'name' => 'Information Security Officer',
                'description' => 'Oversee overall information security strategy and policies. Review security reports, threats, and risk assessments. Ensure compliance with security standards and best practices.',
                'permissions' => [
                    'view_dashboard',
                    'view_all_reports',
                    'view_all_threats',
                    'view_risk_assessments',
                    'manage_policies',
                    'ensure_compliance',
                    'view_audit_logs',
                ]
            ],
            
        ];
    }

    /**
     * Get role name
     */
    public function getRoleNameAttribute()
    {
        $roles = self::getRoles();
        return $roles[$this->role]['name'] ?? ucfirst(str_replace('_', ' ', $this->role));
    }

    /**
     * Get role description
     */
    public function getRoleDescriptionAttribute()
    {
        $roles = self::getRoles();
        return $roles[$this->role]['description'] ?? '';
    }

    /**
     * Get role permissions
     */
    public function getRolePermissionsAttribute()
    {
        $roles = self::getRoles();
        return $roles[$this->role]['permissions'] ?? [];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        return in_array($this->role, (array) $roles);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permission)
    {
        $permissions = $this->role_permissions;
        return in_array($permission, $permissions);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        $userPermissions = $this->role_permissions;
        foreach ((array) $permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions($permissions)
    {
        $userPermissions = $this->role_permissions;
        foreach ((array) $permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return false;
            }
        }
        return true;
    }

    // Role Check Methods
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSecurityAnalyst()
    {
        return $this->role === self::ROLE_SECURITY_ANALYST;
    }

    public function isSystemAdmin()
    {
        return $this->role === self::ROLE_SYSTEM_ADMIN;
    }

    public function isNetworkAdmin()
    {
        return $this->role === self::ROLE_NETWORK_ADMIN;
    }

    public function isITSecurityStaff()
    {
        return $this->role === self::ROLE_IT_SECURITY_STAFF;
    }

    public function isInfoSecurityOfficer()
    {
        return $this->role === self::ROLE_INFO_SECURITY_OFFICER;
    }


    /**
     * Get role badge color
     */
    public function getRoleBadgeColorAttribute()
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'danger',
            self::ROLE_SECURITY_ANALYST => 'primary',
            self::ROLE_SYSTEM_ADMIN => 'warning',
            self::ROLE_NETWORK_ADMIN => 'info',
            self::ROLE_IT_SECURITY_STAFF => 'secondary',
            self::ROLE_INFO_SECURITY_OFFICER => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get role icon
     */
    public function getRoleIconAttribute()
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'fa-user-cog',
            self::ROLE_SECURITY_ANALYST => 'fa-shield-alt',
            self::ROLE_SYSTEM_ADMIN => 'fa-server',
            self::ROLE_NETWORK_ADMIN => 'fa-network-wired',
            self::ROLE_IT_SECURITY_STAFF => 'fa-laptop',
            self::ROLE_INFO_SECURITY_OFFICER => 'fa-user-tie',
            default => 'fa-user',
        };
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }
}