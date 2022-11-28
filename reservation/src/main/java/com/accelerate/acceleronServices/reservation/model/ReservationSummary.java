package com.accelerate.acceleronServices.reservation.model;


import lombok.Data;

@Data
public class ReservationSummary {

    int created;
    int seated;
    int completed;
    int cancelled;
    int total;


    public ReservationSummary() {
        this.created = 0;
        this.seated = 0;
        this.completed = 0;
        this.cancelled = 0;
        this.total = 0;
    }

    public int getCreated() {
        return created;
    }

    public void setCreated(int created) {
        this.created = created;
    }

    public int getTotal() {
        return total;
    }

    public void setTotal() {
        this.total = this.getCreated()+this.getSeated()+this.getCompleted()+this.getCancelled();
    }
}
