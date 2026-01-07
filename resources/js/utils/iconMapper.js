import {
  HomeIcon,
  ChartBarIcon,
  DocumentIcon,
  Cog6ToothIcon,
  UserIcon,
  UsersIcon,
  BuildingOfficeIcon,
  BanknotesIcon,
  ShoppingCartIcon,
  CalendarIcon,
  EnvelopeIcon,
  BellIcon,
  MagnifyingGlassIcon,
  XMarkIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronDownIcon,
  ChevronUpIcon,
  StarIcon as StarSolidIcon,
  StarIcon,
  FolderIcon,
  FolderOpenIcon,
  DocumentTextIcon,
  ListBulletIcon,
  TableCellsIcon,
  ClipboardDocumentListIcon,
  ViewColumnsIcon,
  PresentationChartLineIcon,
  ShieldCheckIcon,
  KeyIcon,
  ServerIcon,
  ComputerDesktopIcon,
  BookmarkIcon,
  BookOpenIcon,
  AcademicCapIcon,
  BriefcaseIcon,
  WalletIcon,
  CreditCardIcon,
  TruckIcon,
  CubeIcon,
  TagIcon,
  HashtagIcon,
  FlagIcon,
  GlobeAltIcon,
  MapPinIcon,
  PhoneIcon,
  ChatBubbleLeftRightIcon,
  PaperClipIcon,
  PhotoIcon,
  FilmIcon,
  MusicalNoteIcon,
  PaintBrushIcon,
  WrenchScrewdriverIcon,
  SparklesIcon,
  LightBulbIcon,
  RocketLaunchIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  PlusIcon,
  MinusIcon,
  PencilIcon,
  TrashIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  ArrowLeftIcon,
  ArrowRightIcon,
  ArrowPathIcon,
  ArrowUturnLeftIcon,
  ArrowUturnRightIcon,
  EyeIcon,
  EyeSlashIcon,
  LockClosedIcon,
  LockOpenIcon,
  KeyIcon as KeyOutlineIcon,
  UserCircleIcon,
  UserGroupIcon,
  IdentificationIcon,
  ClipboardIcon,
  ClipboardDocumentCheckIcon,
  DocumentCheckIcon,
  CheckBadgeIcon,
  ClockIcon,
  CalendarDaysIcon,
  SunIcon,
  MoonIcon,
  CloudIcon,
  BoltIcon,
  FireIcon,
  HeartIcon,
  ThumbUpIcon,
  HandThumbUpIcon,
  HandThumbDownIcon,
  FaceSmileIcon,
  FaceFrownIcon,
} from '@heroicons/vue/24/outline'

import {
  StarIcon as StarSolidIconFilled,
} from '@heroicons/vue/24/solid'

/**
 * Material Design to Heroicons mapping
 * Maps common Material Design icon names to Heroicon components
 */
const materialToHeroiconMap = {
  // Navigation
  'home': HomeIcon,
  'mdi-home': HomeIcon,
  'dashboard': ChartBarIcon,
  'mdi-view-dashboard': ChartBarIcon,
  'folder': FolderIcon,
  'mdi-folder': FolderIcon,
  'folder_open': FolderOpenIcon,
  'mdi-folder-open': FolderOpenIcon,
  
  // Documents
  'description': DocumentTextIcon,
  'mdi-file-document': DocumentTextIcon,
  'document': DocumentIcon,
  'mdi-file': DocumentIcon,
  'article': DocumentTextIcon,
  'mdi-newspaper': DocumentTextIcon,
  'list': ListBulletIcon,
  'mdi-format-list-bulleted': ListBulletIcon,
  'table_chart': TableCellsIcon,
  'mdi-table': TableCellsIcon,
  'assignment': ClipboardDocumentListIcon,
  'mdi-clipboard-text': ClipboardDocumentListIcon,
  'view_list': ViewColumnsIcon,
  'mdi-view-list': ViewColumnsIcon,
  
  // People
  'person': UserIcon,
  'mdi-account': UserIcon,
  'people': UsersIcon,
  'mdi-account-group': UsersIcon,
  'group': UsersIcon,
  'mdi-account-multiple': UsersIcon,
  'person_outline': UserCircleIcon,
  'mdi-account-circle': UserCircleIcon,
  'contacts': UserGroupIcon,
  'mdi-contacts': UserGroupIcon,
  'badge': IdentificationIcon,
  'mdi-badge-account': IdentificationIcon,
  
  // Business
  'business': BuildingOfficeIcon,
  'mdi-office-building': BuildingOfficeIcon,
  'store': BuildingOfficeIcon,
  'mdi-store': BuildingOfficeIcon,
  'corporate_fare': BuildingOfficeIcon,
  'mdi-domain': BuildingOfficeIcon,
  'work': BriefcaseIcon,
  'mdi-briefcase': BriefcaseIcon,
  'account_balance': BanknotesIcon,
  'mdi-bank': BanknotesIcon,
  'money': BanknotesIcon,
  'mdi-cash': BanknotesIcon,
  'payment': CreditCardIcon,
  'mdi-credit-card': CreditCardIcon,
  'wallet': WalletIcon,
  'mdi-wallet': WalletIcon,
  'shopping_cart': ShoppingCartIcon,
  'mdi-cart': ShoppingCartIcon,
  
  // System
  'settings': Cog6ToothIcon,
  'mdi-cog': Cog6ToothIcon,
  'tune': Cog6ToothIcon,
  'mdi-tune': Cog6ToothIcon,
  'admin_panel_settings': ShieldCheckIcon,
  'mdi-shield-account': ShieldCheckIcon,
  'security': KeyIcon,
  'mdi-key': KeyIcon,
  'lock': LockClosedIcon,
  'mdi-lock': LockClosedIcon,
  'lock_open': LockOpenIcon,
  'mdi-lock-open': LockOpenIcon,
  'server': ServerIcon,
  'mdi-server': ServerIcon,
  'computer': ComputerDesktopIcon,
  'mdi-desktop-classic': ComputerDesktopIcon,
  
  // UI Elements
  'star': StarSolidIconFilled,
  'mdi-star': StarSolidIconFilled,
  'star_outline': StarIcon,
  'mdi-star-outline': StarIcon,
  'star_border': StarIcon,
  'mdi-star-outline': StarIcon,
  'bookmark': BookmarkIcon,
  'mdi-bookmark': BookmarkIcon,
  'favorite': HeartIcon,
  'mdi-heart': HeartIcon,
  'favorite_border': HeartIcon,
  'mdi-heart-outline': HeartIcon,
  'book': BookOpenIcon,
  'mdi-book-open': BookOpenIcon,
  'school': AcademicCapIcon,
  'mdi-school': AcademicCapIcon,
  
  // Communication
  'email': EnvelopeIcon,
  'mdi-email': EnvelopeIcon,
  'mail': EnvelopeIcon,
  'mdi-mail': EnvelopeIcon,
  'notifications': BellIcon,
  'mdi-bell': BellIcon,
  'notifications_active': BellIcon,
  'mdi-bell-ring': BellIcon,
  'chat': ChatBubbleLeftRightIcon,
  'mdi-message': ChatBubbleLeftRightIcon,
  'phone': PhoneIcon,
  'mdi-phone': PhoneIcon,
  
  // Actions
  'search': MagnifyingGlassIcon,
  'mdi-magnify': MagnifyingGlassIcon,
  'close': XMarkIcon,
  'mdi-close': XMarkIcon,
  'cancel': XMarkIcon,
  'mdi-cancel': XMarkIcon,
  'chevron_left': ChevronLeftIcon,
  'mdi-chevron-left': ChevronLeftIcon,
  'chevron_right': ChevronRightIcon,
  'mdi-chevron-right': ChevronRightIcon,
  'expand_more': ChevronDownIcon,
  'mdi-chevron-down': ChevronDownIcon,
  'expand_less': ChevronUpIcon,
  'mdi-chevron-up': ChevronUpIcon,
  'arrow_back': ArrowLeftIcon,
  'mdi-arrow-left': ArrowLeftIcon,
  'arrow_forward': ArrowRightIcon,
  'mdi-arrow-right': ArrowRightIcon,
  'arrow_upward': ArrowUpIcon,
  'mdi-arrow-up': ArrowUpIcon,
  'arrow_downward': ArrowDownIcon,
  'mdi-arrow-down': ArrowDownIcon,
  'refresh': ArrowPathIcon,
  'mdi-refresh': ArrowPathIcon,
  'undo': ArrowUturnLeftIcon,
  'mdi-undo': ArrowUturnLeftIcon,
  'redo': ArrowUturnRightIcon,
  'mdi-redo': ArrowUturnRightIcon,
  'edit': PencilIcon,
  'mdi-pencil': PencilIcon,
  'delete': TrashIcon,
  'mdi-delete': TrashIcon,
  'delete_sweep': TrashIcon,
  'mdi-delete-sweep': TrashIcon,
  'add': PlusIcon,
  'mdi-plus': PlusIcon,
  'remove': MinusIcon,
  'mdi-minus': MinusIcon,
  'visibility': EyeIcon,
  'mdi-eye': EyeIcon,
  'visibility_off': EyeSlashIcon,
  'mdi-eye-off': EyeSlashIcon,
  'check_circle': CheckCircleIcon,
  'mdi-check-circle': CheckCircleIcon,
  'cancel': XCircleIcon,
  'mdi-close-circle': XCircleIcon,
  'error': ExclamationTriangleIcon,
  'mdi-alert': ExclamationTriangleIcon,
  'warning': ExclamationTriangleIcon,
  'mdi-alert-circle': ExclamationTriangleIcon,
  'info': InformationCircleIcon,
  'mdi-information': InformationCircleIcon,
  'check': CheckBadgeIcon,
  'mdi-check': CheckBadgeIcon,
  'done': CheckCircleIcon,
  'mdi-check-all': CheckCircleIcon,
  
  // Time & Date
  'event': CalendarIcon,
  'mdi-calendar': CalendarIcon,
  'calendar_today': CalendarDaysIcon,
  'mdi-calendar-today': CalendarDaysIcon,
  'schedule': ClockIcon,
  'mdi-clock': ClockIcon,
  'access_time': ClockIcon,
  'mdi-clock-outline': ClockIcon,
  
  // Other
  'local_offer': TagIcon,
  'mdi-tag': TagIcon,
  'label': TagIcon,
  'mdi-label': TagIcon,
  'category': HashtagIcon,
  'mdi-tag-multiple': HashtagIcon,
  'flag': FlagIcon,
  'mdi-flag': FlagIcon,
  'language': GlobeAltIcon,
  'mdi-web': GlobeAltIcon,
  'location_on': MapPinIcon,
  'mdi-map-marker': MapPinIcon,
  'attachment': PaperClipIcon,
  'mdi-paperclip': PaperClipIcon,
  'image': PhotoIcon,
  'mdi-image': PhotoIcon,
  'movie': FilmIcon,
  'mdi-movie': FilmIcon,
  'music_note': MusicalNoteIcon,
  'mdi-music': MusicalNoteIcon,
  'palette': PaintBrushIcon,
  'mdi-palette': PaintBrushIcon,
  'build': WrenchScrewdriverIcon,
  'mdi-wrench': WrenchScrewdriverIcon,
  'auto_awesome': SparklesIcon,
  'mdi-auto-fix': SparklesIcon,
  'lightbulb': LightBulbIcon,
  'mdi-lightbulb': LightBulbIcon,
  'rocket_launch': RocketLaunchIcon,
  'mdi-rocket-launch': RocketLaunchIcon,
  'local_shipping': TruckIcon,
  'mdi-truck': TruckIcon,
  'inventory': CubeIcon,
  'mdi-cube': CubeIcon,
  'inventory_2': CubeIcon,
  'mdi-cube-outline': CubeIcon,
  'assessment': PresentationChartLineIcon,
  'mdi-chart-line': PresentationChartLineIcon,
  'analytics': ChartBarIcon,
  'mdi-chart-bar': ChartBarIcon,
  'insights': ChartBarIcon,
  'mdi-trending-up': ChartBarIcon,
  
  // Status
  'thumb_up': ThumbUpIcon,
  'mdi-thumb-up': ThumbUpIcon,
  'thumb_down': HandThumbDownIcon,
  'mdi-thumb-down': HandThumbDownIcon,
  'sentiment_satisfied': FaceSmileIcon,
  'mdi-emoticon-happy': FaceSmileIcon,
  'sentiment_dissatisfied': FaceFrownIcon,
  'mdi-emoticon-sad': FaceFrownIcon,
  
  // Weather/Styling
  'brightness_5': SunIcon,
  'mdi-weather-sunny': SunIcon,
  'brightness_2': MoonIcon,
  'mdi-weather-night': MoonIcon,
  'cloud': CloudIcon,
  'mdi-cloud': CloudIcon,
  'bolt': BoltIcon,
  'mdi-lightning-bolt': BoltIcon,
  'local_fire_department': FireIcon,
  'mdi-fire': FireIcon,
}

/**
 * Direct Heroicon name to component mapping
 * Supports both heroicon-o-* and heroicon-* formats
 */
const heroiconNameMap = {
  // Navigation
  'home': HomeIcon,
  'heroicon-o-home': HomeIcon,
  'dashboard': ChartBarIcon,
  'heroicon-o-chart-bar': ChartBarIcon,
  'folder': FolderIcon,
  'heroicon-o-folder': FolderIcon,
  
  // Documents
  'document': DocumentIcon,
  'heroicon-o-document': DocumentIcon,
  'document-text': DocumentTextIcon,
  'heroicon-o-document-text': DocumentTextIcon,
  
  // People
  'user': UserIcon,
  'heroicon-o-user': UserIcon,
  'users': UsersIcon,
  'heroicon-o-users': UsersIcon,
  
  // System
  'cog': Cog6ToothIcon,
  'heroicon-o-cog-6-tooth': Cog6ToothIcon,
  'settings': Cog6ToothIcon,
  'heroicon-o-cog-6-tooth': Cog6ToothIcon,
}

/**
 * Get Heroicon component from icon name
 * Supports both Material Design icon names and Heroicon names
 * 
 * @param {string} iconName - Icon name (Material Design or Heroicon format)
 * @returns {Object|null} - Heroicon component or null if not found
 */
export function getIconComponent(iconName) {
  if (!iconName) {
    return null
  }
  
  // Normalize icon name (lowercase, trim)
  const normalized = iconName.toString().toLowerCase().trim()
  
  // Check Material Design mapping first
  if (materialToHeroiconMap[normalized]) {
    return materialToHeroiconMap[normalized]
  }
  
  // Check Heroicon name mapping
  if (heroiconNameMap[normalized]) {
    return heroiconNameMap[normalized]
  }
  
  // Try removing heroicon-o- or heroicon- prefix
  const withoutPrefix = normalized.replace(/^heroicon-o?-/, '')
  if (materialToHeroiconMap[withoutPrefix]) {
    return materialToHeroiconMap[withoutPrefix]
  }
  
  // Try removing mdi- prefix
  const withoutMdi = normalized.replace(/^mdi-/, '')
  if (materialToHeroiconMap[withoutMdi]) {
    return materialToHeroiconMap[withoutMdi]
  }
  
  // Fallback to common icon
  console.warn(`Icon "${iconName}" not found in icon mapper. Using default DocumentIcon.`)
  return DocumentIcon
}

/**
 * Check if an icon name is a Material Design icon
 */
export function isMaterialIcon(iconName) {
  if (!iconName) return false
  const normalized = iconName.toString().toLowerCase().trim()
  return normalized.startsWith('mdi-') || 
         materialToHeroiconMap[normalized] !== undefined ||
         materialToHeroiconMap[normalized.replace(/^mdi-/, '')] !== undefined
}

/**
 * Check if an icon name is a Heroicon
 */
export function isHeroicon(iconName) {
  if (!iconName) return false
  const normalized = iconName.toString().toLowerCase().trim()
  return normalized.startsWith('heroicon-') || 
         heroiconNameMap[normalized] !== undefined ||
         heroiconNameMap[normalized.replace(/^heroicon-o?-/, '')] !== undefined
}

