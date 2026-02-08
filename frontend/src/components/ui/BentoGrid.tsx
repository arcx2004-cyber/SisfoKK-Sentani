import { motion } from "framer-motion";
import type { ReactNode } from "react";

interface BentoGridProps {
    children: ReactNode;
    className?: string;
}

export const BentoGrid = ({ children, className = "" }: BentoGridProps) => {
    return (
        <div className={`bento-grid ${className}`}>
            {children}
        </div>
    );
};

interface BentoCardProps {
    title: string;
    description?: string;
    header?: ReactNode;
    icon?: ReactNode;
    className?: string;
    span?: string;
    index: number;
}

export const BentoCard = ({
    title,
    description,
    header,
    icon,
    className = "",
    span = "col-span-1",
    index,
}: BentoCardProps) => {
    return (
        <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{
                duration: 0.5,
                delay: index * 0.1,
                ease: [0.21, 0.47, 0.32, 0.98],
            }}
            className={`bento-item ${span} ${className}`}
        >
            <div className="flex flex-col h-full">
                {header && <div className="mb-4">{header}</div>}
                <div className="flex items-center gap-2 mb-2">
                    {icon && <div className="text-primary-500">{icon}</div>}
                    <h3 className="text-xl font-bold font-heading text-gray-900 line-clamp-1">{title}</h3>
                </div>
                {description && (
                    <p className="text-gray-600 text-sm line-clamp-3 mb-4">{description}</p>
                )}
            </div>
        </motion.div>
    );
};

