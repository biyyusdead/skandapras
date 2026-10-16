import { createIcons, icons } from 'lucide';

const usedIcons = {
    // ============================================
    // NAVIGATION
    // ============================================
    Home: icons.Home,
    Menu: icons.Menu,
    X: icons.X,
    ChevronDown: icons.ChevronDown,
    ChevronRight: icons.ChevronRight,
    ChevronLeft: icons.ChevronLeft,
    ChevronUp: icons.ChevronUp,
    ArrowRight: icons.ArrowRight,
    ArrowLeft: icons.ArrowLeft,
    ArrowUp: icons.ArrowUp,
    ArrowDown: icons.ArrowDown,
    ArrowUpRight: icons.ArrowUpRight,
    ExternalLink: icons.ExternalLink,
    MoreVertical: icons.MoreVertical,
    MoreHorizontal: icons.MoreHorizontal,

    // ============================================
    // USER & PROFILE
    // ============================================
    User: icons.User,
    UserCircle: icons.UserCircle,
    UserCog: icons.UserCog,
    UserPlus: icons.UserPlus,
    LogOut: icons.LogOut,
    LogIn: icons.LogIn,
    Settings: icons.Settings,
    Bell: icons.Bell,
    BellOff: icons.BellOff,

    // ============================================
    // SOCIAL MEDIA
    // ============================================
    Linkedin: icons.Linkedin,
    Instagram: icons.Instagram,
    Youtube: icons.Youtube,
    Facebook: icons.Facebook,
    Twitter: icons.Twitter,
    Github: icons.Github,
    Send: icons.Send,

    // ============================================
    // SEARCH & FILTER
    // ============================================
    Search: icons.Search,
    SearchX: icons.SearchX,
    Filter: icons.Filter,
    Eye: icons.Eye,
    EyeOff: icons.EyeOff,
    Globe: icons.Globe,

    // ============================================
    // MEDIA
    // ============================================
    Image: icons.Image,
    ImagePlus: icons.ImagePlus,
    Upload: icons.Upload,
    Download: icons.Download,
    Play: icons.Play,
    Video: icons.Video,
    Camera: icons.Camera,
    Music: icons.Music,
    Volume2: icons.Volume2,
    VolumeX: icons.VolumeX,

    // ============================================
    // STATUS
    // ============================================
    Check: icons.Check,
    CheckCircle: icons.CheckCircle,
    CheckCheck: icons.CheckCheck,
    XCircle: icons.XCircle,
    AlertCircle: icons.AlertCircle,
    AlertTriangle: icons.AlertTriangle,
    Info: icons.Info,
    HelpCircle: icons.HelpCircle,

    // ============================================
    // TIME
    // ============================================
    Calendar: icons.Calendar,
    Clock: icons.Clock,
    History: icons.History,
    Timer: icons.Timer,

    // ============================================
    // COMMUNICATION
    // ============================================
    Mail: icons.Mail,
    Mailbox: icons.Mailbox,
    Phone: icons.Phone,
    MessageSquare: icons.MessageSquare,
    MessageCircle: icons.MessageCircle,
    Printer: icons.Printer,
    Inbox: icons.Inbox,

    // ============================================
    // LOCATION & MAP
    // ============================================
    MapPin: icons.MapPin,
    MapPinOff: icons.MapPinOff,
    MapPinned: icons.MapPinned,
    Map: icons.Map,
    Layers: icons.Layers,
    Compass: icons.Compass,
    Navigation: icons.Navigation,

    // ============================================
    // EDUCATION
    // ============================================
    GraduationCap: icons.GraduationCap,
    School: icons.School,
    BookOpen: icons.BookOpen,
    Book: icons.Book,
    Award: icons.Award,
    Trophy: icons.Trophy,
    Crown: icons.Crown,
    Medal: icons.Medal,
    Star: icons.Star,

    // ============================================
    // BUILDING
    // ============================================
    Building: icons.Building,
    Building2: icons.Building2,
    DoorOpen: icons.DoorOpen,
    DoorClosed: icons.DoorClosed,

    // ============================================
    // TECH
    // ============================================
    Server: icons.Server,
    Cpu: icons.Cpu,
    Code: icons.Code,
    Code2: icons.Code2,
    Network: icons.Network,
    Wrench: icons.Wrench,
    Calculator: icons.Calculator,
    Palette: icons.Palette,
    Briefcase: icons.Briefcase,
    Rocket: icons.Rocket,

    // ============================================
    // CONTENT
    // ============================================
    Newspaper: icons.Newspaper,
    FileText: icons.FileText,
    FilePenLine: icons.FilePenLine,
    File: icons.File,
    Files: icons.Files,
    Quote: icons.Quote,
    Hash: icons.Hash,
    Tag: icons.Tag,
    Tags: icons.Tags,
    Link: icons.Link,
    Link2: icons.Link2,

    // ============================================
    // ACTIONS
    // ============================================
    Plus: icons.Plus,
    Minus: icons.Minus,
    Edit: icons.Edit,
    Edit2: icons.Edit2,
    Edit3: icons.Edit3,
    Trash: icons.Trash,
    Trash2: icons.Trash2,
    Save: icons.Save,
    Copy: icons.Copy,
    Share: icons.Share,
    Share2: icons.Share2,
    Bookmark: icons.Bookmark,
    Heart: icons.Heart,
    ThumbsUp: icons.ThumbsUp,

    // ============================================
    // LAYOUT
    // ============================================
    LayoutDashboard: icons.LayoutDashboard,
    LayoutGrid: icons.LayoutGrid,
    LayoutList: icons.LayoutList,
    Grid: icons.Grid,
    List: icons.List,
    PanelLeft: icons.PanelLeft,
    PanelRight: icons.PanelRight,
    Maximize: icons.Maximize,
    Minimize: icons.Minimize,
    Expand: icons.Expand,

    // ============================================
    // MISC
    // ============================================
    HeartPulse: icons.HeartPulse,
    Target: icons.Target,
    Zap: icons.Zap,
    Sparkles: icons.Sparkles,
    Flame: icons.Flame,
    Gift: icons.Gift,
    Sun: icons.Sun,
    Moon: icons.Moon,
    Loader: icons.Loader,
    Loader2: icons.Loader2,
    RefreshCw: icons.RefreshCw,
    RotateCw: icons.RotateCw,
    MousePointer: icons.MousePointer,
    MousePointer2: icons.MousePointer2,
    Lightbulb: icons.Lightbulb,
    Palette2: icons.Palette,
    Quote2: icons.Quote,

    // ============================================
    // SOCIAL MEDIA — TAMBAHAN
    // ============================================
    Linkedin: icons.Linkedin,
    Instagram: icons.Instagram,
    Youtube: icons.Youtube,
    Facebook: icons.Facebook,
    Twitter: icons.Twitter,
    Github: icons.Github,
    Send: icons.Send,

    // ============================================
    // EDIT & ACTIONS — TAMBAHAN
    // ============================================
    Edit: icons.Edit,
    Edit2: icons.Edit2,
    Edit3: icons.Edit3,
    Pencil: icons.Pencil,       // ← kalau pakai "pencil"
    Pen: icons.Pen,              // ← atau "pen"
    PenLine: icons.PenLine,
    Save: icons.Save,
    Trash: icons.Trash,
    Trash2: icons.Trash2,
    Plus: icons.Plus,
    Minus: icons.Minus,
    X: icons.X,
    Check: icons.Check,
    Copy: icons.Copy,
    Download: icons.Download,
    Upload: icons.Upload,
    Eye: icons.Eye,
    EyeOff: icons.EyeOff,
    Filter: icons.Filter,

    // TAMBAHAN — fix icon yang hilang
    Package: icons.Package,
    Pencil: icons.Pencil,
    Trash2: icons.Trash2,
    MousePointerClick: icons.MousePointerClick,
    };

let initialized = false;

export function initLucide(force = false) {
    if (!force && initialized) return;
    try {
        createIcons({ icons });
        initialized = true;
    } catch (e) {
        console.warn('[Lucide] init error:', e);
    }
}

export function refreshLucide() {
    initialized = false;
    initLucide(true);
}

window.initLucide = initLucide;
window.refreshLucide = refreshLucide;