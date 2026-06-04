export default (target) => ({
    target,
    days: 0, hours: 0, minutes: 0, seconds: 0,
    tick() {
        const diff = this.target - Date.now()
        if (diff <= 0) {
            this.days = this.hours = this.minutes = this.seconds = 0
            return
        }
        this.days = Math.floor(diff / 86400000)
        this.hours = Math.floor(diff % 86400000 / 3600000)
        this.minutes = Math.floor(diff % 3600000 / 60000)
        this.seconds = Math.floor(diff % 60000 / 1000)
    },
    init() {
        this.tick()
        const id = setInterval(() => this.tick(), 1000)
        this.$cleanup(() => clearInterval(id))
    }
})
