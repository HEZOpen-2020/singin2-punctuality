#include "valcomp.h"
///// BEGIN Fake Sometimes TLE Library Treap /////
namespace F_stl {
    #ifndef _FSTL_LIB_TREAP
    #define _FSTL_LIB_TREAP
    //Treap节点（请不要自行创建）
    template<class T,class ET>
    struct TreapNode {
        //懒标记类
        ET tag;

        //父子关系指针
        TreapNode *fa,*ch[2];

        //数值
        T val;
        //权值
        int rank;
        //大小
        int sz;
        //临时值
        //用于在依据指针拆分时标记到达结点的路径
        int tmp;

        //构造默认节点
        TreapNode() {
            sz=0;
            ch[0]=ch[1]=fa=NULL;
            rank=rand();
        }
        //构造带数值节点
        TreapNode(T _val) {
            sz=1;
            val=_val;
            ch[0]=ch[1]=fa=NULL;
            rank=rand();
        }
        //确定位置
        int identify() {
            if(!fa) return -1;
            if(fa->ch[0]==this) return 0;
            return 1;
        }
    };

    //懒标记类
    struct TreapRange_void {
        inline static bool enable() {return 0;}
        inline bool notNull() {return 0;}
        inline void add(TreapRange_void t) {}
        template<class Tp>
        inline void apply(Tp *ptr) {}
        inline void clear() {}
    };

    //通用FHQ Treap平衡树类（指针，完整迭代器。调用传入class T的小于号）
    //递归结构，同值不同存
    //  - ET: 懒标记类，定义区间操作的行为
    //  - CT: 小于号类，定义比较方式
    //注意：当!(x<y) && !(y<x)时，认为x==y，此时x和y的顺序不确定。建议使小于号严格区分两个不同的值
    template<class T,class ET=TreapRange_void,class CT=ValComp_less<T> >
    struct Treap {
    private:
        //定义使用的节点
        typedef TreapNode<T,ET> Node;

        //使用的比较器
        CT comp;

        //无穷大
        static const int INF=0x7fffffff;

        //是否启用懒标记
        bool enable_et;

        //树的超级根
        Node *rt;

    public:
        //参数：删除操作>删除全部数
        static const bool ERASE_ALL=true;

        //参数：删除操作>删除一个数
        static const bool ERASE_ONE=false;

    private:
        //下传懒标记类
        inline void push_down(Node *p) {
            if(!enable_et) return;
            if(!p) return;
            if(!(p->tag.notNull())) return;
            if(p->ch[0]) p->ch[0]->tag.add(p->tag);
            if(p->ch[1]) p->ch[1]->tag.add(p->tag);
            p->tag.apply(p);
            p->tag.clear();
        }

        //上传元素个数信息
        inline void push_up(Node *p) {
            push_down(p->ch[0]);push_down(p->ch[1]);
            p->sz = (p->ch[0]?p->ch[0]->sz:0)+(p->ch[1]?p->ch[1]->sz:0)+1;
            if(p->ch[0]) p->ch[0]->fa=p;
            if(p->ch[1]) p->ch[1]->fa=p;
        }

        //拆分（依据：存储值）
        //  - 参数flag：如果为false，将拆分val==curr->val到左侧（默认拆分），否则拆分val==curr->val到右侧（该参数用于erase等函数，以单独拆分出某个值。并不是任何类型都可以x-1）
        void split(int val,Node *curr,Node *&L,Node *&R,bool flag=0) {
            if(!curr) {L=R=NULL;return;}
            push_down(curr);
            if(flag?comp(curr->val,val):(!comp(val,curr->val))) {
                L=curr;
                split(val,curr->ch[1],curr->ch[1],R,flag);
            }
            else {
                R=curr;
                split(val,curr->ch[0],L,curr->ch[0],flag);
            }
            push_up(curr);
        }

        //拆分（依据：排名）
        //由于排名数可以x-1，无需flag
        void rk_split(int rk,Node *curr,Node *&L,Node *&R) {
            if(!curr) {L=R=NULL;return;}
            push_down(curr);
            int lsz=(curr->ch[0]?(curr->ch[0]->sz):(0));
            if(rk<=lsz) {
                R=curr;
                rk_split(rk,curr->ch[0],L,curr->ch[0]);
            }
            else {
                L=curr;
                rk_split(rk-lsz-1,curr->ch[1],curr->ch[1],R);
            }
            push_up(curr);
        }

        //拆分（依据：tmp值）
        //该拆分是iter_split的子函数，不单独使用。
        void tmp_split(Node *curr,Node *&L,Node *&R,bool flag=0,int tt=-1) {
            if(!curr) {L=R=NULL;return;}
            push_down(curr);
            if((curr->tmp==0 || (flag && curr->tmp==-1) || tt==0) && tt!=1) {
                R=curr;
                if(curr->tmp==-1 && tt==-1) tt=1;
                tmp_split(curr->ch[0],L,curr->ch[0],flag,tt);
            }
            else {
                L=curr;
                if(curr->tmp==-1 && tt==-1) tt=0;
                tmp_split(curr->ch[1],curr->ch[1],R,flag,tt);
            }
            push_up(curr);
        }

        //拆分（依据：指针）
        //  - 参数flag：如果为false，将拆分给定的指针到左侧（默认拆分），否则拆分给定的指针到右侧（该参数用于erase等函数，以单独拆分出某个值。指针不方便x-1）
        inline void iter_split(Node *t,Node *h,Node *&L,Node *&R,bool flag=0) {
            t->tmp = -1;
            while(t->fa) {
                t->fa->tmp = t->identify();
                t = t->fa;
            }
            tmp_split(h,L,R,flag);
        }

        //合并（依据：随机权值）
        Node *merge(Node *a,Node *b) {
            if(a) push_down(a);
            if(b) push_down(b);
            if(!b) return a;
            if(!a) return b;
            if((a->rank) < (b->rank)) {
                a->ch[1] = merge(a->ch[1],b);
                push_up(a);
                return a;
            }
            else {
                b->ch[0] = merge(a,b->ch[0]);
                push_up(b);
                return b;
            }
        }

        //清除节点和子树，释放空间
        void __destroyTree(Node *t) {
            if(!t) return; // 某些情况可能会被传入NULL，要忽略掉
            push_down(t);
            if(t->ch[0]) __destroyTree(t->ch[0]);
            if(t->ch[1]) __destroyTree(t->ch[1]);
            delete t;
        }

    public:
        //迭代器（使用方法同STL __RbTree_iterator）
        //支持操作：取位置 *it，自加 it++，自减 it--，比较 it==other，判断有效性 it.isNull()
        //区间操作如果不影响结构，则不影响iterator的迭代。
        //如果iterator被erase，将不可再用于迭代。正确的，使it指向下个结点的方法如下：
        //  Tp::iterator bk = it; ++bk; tr.erase(it); it=bk;
        struct iterator {
        public:
            Node *it;
            Treap<T,ET,CT> *tr;
            iterator() {it=NULL;}
            iterator(Node *_it,Treap<T,ET,CT> *_tr) {it=_it;tr=_tr;}

            //判定是否NULL
            inline bool isNull() const {return !(it);}

            //取值（返回只读引用值。注意不可以试图直接修改，会GG）
            inline const T &operator*() const {
                return it->val;
            }

            //前自加
            inline iterator &operator++() {
                (*this) = (*this)+1;
                return *this;
            }

            //后自加
            inline iterator operator++(signed __suf) { //此处signed类型防止 #define int ll 导致出锅
                iterator ret=(*this);
                ++(*this);
                return ret;
            }

            //前自减
            inline iterator &operator--() {
                (*this) = (*this)-1;
                return *this;
            }

            //后自减
            inline iterator operator--(signed __suf) {
                iterator ret=(*this);
                --(*this);
                return ret;
            }

            //相等（等价）
            inline bool operator==(const iterator &other) const {
                return it==other.it && tr==other.tr;
            }

            //不相等（不等价）
            inline bool operator!=(const iterator &other) const {
                return !operator==(other);
            }

            //无效（定义：isNull()）
            inline bool operator!() const {
                return isNull();
            }

            //后移迭代器
            inline iterator operator+(const int &other) const {
                if(isNull()) return iterator(NULL,tr);
                return tr->atrk(tr->idx(*this) + 1 + other);
            }
            inline iterator operator+=(const int &other) {
                (*this) = (*this)+other;
                return (*this);
            }

            //前移迭代器
            inline iterator operator-(const int &other) const {
                if(isNull()) return iterator(NULL,tr);
                return tr->atrk(tr->idx(*this) + 1 - other);
            }
            inline iterator operator-=(const int &other) {
                (*this) = (*this)-other;
                return (*this);
            }

            //计算迭代器间距离
            inline int operator-(const iterator &other) const {
                if(isNull() || other.isNull()) return -1;
                if(tr != other.tr) return -1;
                return tr->idx(*this) - other.tr->idx(other);
            }

            //指针操作（不要修改！）
            inline const Node *operator->() const {
                return &(it->val);
            }
        };

        //正向开始（指向第一个元素，没有则NULL）
        inline iterator begin() {
            return atrk(1);
        }

        //正向结束（指向最后一个元素后的位置，实则是NULL）
        inline iterator end() {
            return iterator(NULL,this);
        }

        //反向开始（指向最后一个元素，没有则NULL）
        //注意返回的仍然是iterator（此时应使用--符号迭代）。Treap没有reverse_iterator。
        inline iterator rbegin() {
            return atrk(size());
        }

        //反向结束（指向第一个元素前的位置，实则是NULL）
        inline iterator rend() {
            return iterator(NULL,this);
        }

        //清空树（放心食用）
        inline void clear() {
            __destroyTree(rt->ch[1]);
            rt->ch[1] = NULL;
        }

        //取大小
        inline int size() {
            if(!rt->ch[1]) return 0;
            push_down(rt->ch[1]);
            return rt->ch[1]->sz;
        }

        //构造空Treap
        Treap() {
            rt=new Node();
            rt->rank=-INF;
            enable_et = ET::enable();
        }

        //查找数值val，返回第一个位置的迭代器
        //  - 参数flag：控制返回第一个迭代器（0）还是最后一个迭代器（1）
        inline iterator find(T val,bool flag=0) {
            Node *x,*y,*ret;
            if(!flag) {
                split(val,rt->ch[1],x,y,true);
                ret = y;
                while(ret && ret->ch[0]) ret = ret->ch[0];
            }
            else {
                split(val,rt->ch[1],x,y);
                ret = x;
                while(ret && ret->ch[1]) ret = ret->ch[1];
            }
            rt->ch[1] = merge(x,y);
            if(ret && !comp(ret->val,val) && !comp(val,ret->val)) return iterator(ret,this);
            else return iterator(NULL,this);
        }

        //插入数值，返回插入处的迭代器
        //拆分-->插入-->合并，维护堆性质
        //插入数已经存在时，仍然创建新点
        //理论上不会返回NULL（除非你的编译命令是cxk）
        inline iterator insert(T val) {
            Node *L,*R;
            split(val,rt->ch[1],L,R);
            Node *ret = new Node(val);
            rt->ch[1] = merge(merge(L,ret),R);
            return iterator(ret,this);
        }

        //在迭代器所指位置处插入数值（可能破坏BST性质，破坏后Treap不得再进行基于值的操作）
        //  - 参数flag: 如果为0，插入位置在迭代器前，否则在其后
        inline iterator insert(iterator pos,T val,bool flag=0) {
            Node *L,*R;
            iter_split(pos.it,rt->ch[1],L,R,!flag);
            Node *ret = new Node(val);
            rt->ch[1] = merge(merge(L,ret),R);
            return iterator(ret,this);
        }

        //在排名所指位置处插入数值（可能破坏BST性质，破坏后Treap不得再进行基于值的操作）
        //  - 参数flag: 如果为0，插入位置在指定位置前，否则在其后
        inline iterator rk_insert(int pos,T val,bool flag=0) {
            Node *L,*R;
            rk_split(flag?(pos):(pos-1),rt->ch[1],L,R);
            Node *ret = new Node(val);
            rt->ch[1] = merge(merge(L,ret),R);
            return iterator(ret,this);
        }

        //查询数值的出现次数
        inline int count(T val) {
            Node *x,*y,*z;
            split((val),rt->ch[1],x,z);
            split((val),x,x,y,true);
            int ret = y?(y->sz):(0);
            rt->ch[1] = merge(merge(x,y),z);
            return ret;
        }

        //删除迭代器处的数值（会释放空间）
        //注意，迭代器在删除后不再可用。
        //  - 参数tp：默认false。
        //     Treap::ERASE_ONE==false，移除一个相应值的结点
        //     Treap::ERASE_ALL==true，移除所有相应值的结点
        inline void erase(iterator it,bool tp=false) {
            Node *x,*y,*z;
            if(tp) {
                split((*it),rt->ch[1],x,z);
                split((*it),x,x,y,true); //拆分出包含所有当前值的子树
                __destroyTree(y); //删除全部。由于数值已经拆分出，删除子树即可。
                rt->ch[1] = merge(x,z);
            }
            else {
                iter_split(it.it,rt->ch[1],x,z);
                iter_split(it.it,x,x,y,true); //拆分出给定的迭代器指向的结点
                //print_r(x);print_r(y);print_r(z);
                assert(y);
                Node *y_ = y;
                y = merge(y->ch[0],y->ch[1]);
                delete y_;
                rt->ch[1] = merge(merge(x,y),z);
            }
        }

        //删除数值（会释放空间）
        //注意，建议不要边迭代边用这个函数。该函数用于卡常。
        //  - 参数tp：默认false。
        //     Treap::ERASE_ONE==false，移除一个相应值的结点
        //     Treap::ERASE_ALL==true，移除所有相应值的结点
        inline void erase_value(T val,bool tp=false) {
            Node *x,*y,*z;
            split(val,rt->ch[1],x,z);
            split(val,x,x,y,true); //拆分出包含所有当前值的子树
            if(tp) {__destroyTree(y);y=NULL;} //全部删除
            else   {Node *y_=y;y=merge(y->ch[0],y->ch[1]);delete y_;} //删除根
            rt->ch[1] = merge(merge(x,y),z);
        }

        //排名区间删除
        inline void erase_rk(int lrk,int rrk) {
            Node *x,*y,*z;
            rk_split(rrk,rt->ch[1],x,z);
            rk_split(lrk-1,x,x,y);
            __destroyTree(y);
            rt->ch[1] = merge(x,z);
        }

        //迭代器区间删除
        inline void erase(iterator liter,iterator riter) {
            Node *x,*y,*z;
            iter_split(riter.it,rt->ch[1],x,z);
            iter_split(liter.it,x,x,y,true);
            __destroyTree(y);
            rt->ch[1] = merge(x,z);
        }

        //获取元素的排名（开始于1）。定义：小于给定值的数量+1
        inline int rk(T val) {
            Node *x,*y;
            split(val,rt->ch[1],x,y,true);
            int ret = (x?(x->sz):(0)) + 1;
            rt->ch[1] = merge(x,y);
            return ret;
        }

        //获取排名为urk的元素（开始于1）。越界则返回NULL 
        inline iterator atrk(int urk) {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL,this);
            while(1) {
                push_down(curr);
                int lsz=(curr->ch[0]?curr->ch[0]->sz:0);
                int l= lsz+1;
                if((urk>lsz) && urk<=l) break;
                if(urk<l) curr=curr->ch[0];
                else {
                    urk-=l;
                    curr=curr->ch[1];
                }
                if(!curr) return iterator(NULL,this);
            }
            return iterator(curr,this);
        }

        //查找小于x且最大的元素
        inline iterator prev_value(T val) {
            Node *x,*y;
            split(val,rt->ch[1],x,y,true);
            Node *ret=x;
            while(ret && ret->ch[1]) ret=ret->ch[1];
            rt->ch[1]=merge(x,y);
            return iterator(ret,this);
        }

        //查找大于x且最小的元素
        inline iterator next_value(T val) {
            Node *x,*y;
            split(val,rt->ch[1],x,y);
            Node *ret=y;
            while(ret && ret->ch[0]) ret=ret->ch[0];
            rt->ch[1]=merge(x,y);
            return iterator(ret,this);
        }

        //查找upper_bound，返回指针（定义同STL upper_bound）
        inline iterator upper_bound(T val) {
            return next_value(val);
        }

        //查找lower_bound，返回指针（定义同STL lower_bound）
        inline iterator lower_bound(T val) {
            Node *x,*y;
            split(val,rt->ch[1],x,y,true);
            Node *ret=y;
            while(ret && ret->ch[0]) ret=ret->ch[0];
            rt->ch[1]=merge(x,y);
            return iterator(ret,this);
        }

        //执行区间操作
        //实际行为以“懒标记类”定义。如果是破坏BST性质的定义，那么该Treap不该用来执行基于值的操作。
        inline void operation(iterator liter,iterator riter,ET xxx) {
            Node *x,*y,*z;
            iter_split(riter.it,rt->ch[1],x,z);
            iter_split(liter.it,x,x,y,true);
            // print_r(x);print_r(y);print_r(z);
            if(y) y->tag.add(xxx);
            rt->ch[1] = merge(merge(x,y),z);
        }

        //执行排名区间操作
        //实际行为以“懒标记类”定义。如果是破坏BST性质的定义，那么该Treap不该用来执行基于值的操作。
        inline void operation(int lrk,int rrk,ET xxx) {
            Node *x,*y,*z;
            rk_split(rrk,rt->ch[1],x,z);
            rk_split(lrk-1,x,x,y);
            // print_r(x);print_r(y);print_r(z);
            if(y) y->tag.add(xxx);
            rt->ch[1] = merge(merge(x,y),z);
        }

        //访问下标（定义：atrk(idx+1)）
        inline const T &operator[] (int idx)  {
            return (*atrk(idx+1));
        }

        //获取下标（定义：在迭代器左边数量）
        inline int idx(iterator it) {
            if(it.isNull()) return -1;
            Node *x,*y;
            iter_split(it.it,rt->ch[1],x,y,true);
            // print_r(x);print_r(y);
            int ret=x?(x->sz):0;
            rt->ch[1]=merge(x,y);
            return ret;
        }

        //等值区间（定义：lower_bound ~ upper_bound）
        inline pair<iterator,iterator> equal_range(T val) {
            return make_pair(lower_bound(val),upper_bound(val));
        }

    private:
        //输出序列-递归函数
        int dump_idx;
        void __print(Node *n) {
            push_down(n);
            if(n->ch[0]) __print(n->ch[0]);
            dump_idx++;
            cout<<dump_idx<<"\t"<<(n->val)<<"\t"<<n<<endl;
            if(n->ch[1]) __print(n->ch[1]);
        }

    public:
        //输出序列
        void print_r(Node *t=NULL) {
            if(!t) {
                cout<<"Tree is empty."<<endl;
                return;
            }
            cout<<"Index\tValue\tPointer"<<endl;
            dump_idx=0;
            __print(t);
        }

        //输出真序列
        void print() {print_r(rt->ch[1]);} 
    };
    #endif
};
///// END Fake Sometimes TLE Library Treap /////
