export function dd2dms(D: number, lng?: boolean){
    let dir = D<0?lng?'W':'S':lng?'E':'N',
        deg = 0|(D<0?D=-D:D),
        min = 0|D%1*60,
        sec = (0|D*60%1*6000)/100;

    return `${deg}°${min}"${sec}' ${dir}`;
}

export function triggerResize() {
    window.dispatchEvent(new Event('resize'));
}
