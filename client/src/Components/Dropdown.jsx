import { useState, createContext, useContext, Fragment } from 'react';
import { Link } from '@inertiajs/react';

const DropDownContext = createContext();

const Dropdown = ({ children }) => {
    const [open, setOpen] = useState(false);

    const toggleOpen = () => {
        setOpen((previousState) => !previousState);
    };

    return (
        <DropDownContext.Provider value={{ open, setOpen, toggleOpen }}>
            <div className="dropdown">{children}</div>
        </DropDownContext.Provider>
    );
};

const Trigger = ({ children }) => {
    const { open, setOpen, toggleOpen } = useContext(DropDownContext);

    return (
        <>
            <div onClick={toggleOpen}>{children}</div>

            {open && <div className="fixed inset-0 z-40" onClick={() => setOpen(false)}></div>}
        </>
    );
};

const Content = ({ align = 'right', width = '48', contentClasses = '', children }) => {
    const { open, setOpen } = useContext(DropDownContext);

    const alignmentClass = align === 'left' ? 'dropdown-content-left' : 'dropdown-content-right';
    const widthClass = width === '48' ? 'dropdown-content-w48' : '';

    return (
        <>
            {open && (
                <div className={`dropdown-content ${alignmentClass} ${widthClass}`} onClick={() => setOpen(false)}>
                    <div className={`dropdown-content-inner ${contentClasses}`}>{children}</div>
                </div>
            )}
        </>
    );
};

const DropdownLink = ({ className = '', children, ...props }) => {
    return (
        <Link
            {...props}
            className={'dropdown-link ' + className}
        >
            {children}
        </Link>
    );
};

Dropdown.Trigger = Trigger;
Dropdown.Content = Content;
Dropdown.Link = DropdownLink;

export default Dropdown;
